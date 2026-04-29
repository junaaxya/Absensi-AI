<?php

namespace App\Http\Controllers;

use App\Models\FormTemplate;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminFormController extends Controller
{
    public function index()
    {
        $templates = FormTemplate::withCount('submissions')
            ->with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.forms.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'fields' => 'required|json',
            'requires_approval' => 'boolean',
            'approval_roles' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        $fields = json_decode($validated['fields'], true);
        if (empty($fields)) {
            return back()->withErrors(['fields' => 'Minimal satu field harus ditambahkan.'])->withInput();
        }

        FormTemplate::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
            'description' => $validated['description'] ?? null,
            'fields' => $fields,
            'requires_approval' => $request->boolean('requires_approval'),
            'approval_roles' => !empty($validated['approval_roles']) ? json_decode($validated['approval_roles'], true) : null,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Template form berhasil dibuat.');
    }

    public function edit(FormTemplate $template)
    {
        return view('admin.forms.edit', compact('template'));
    }

    public function update(Request $request, FormTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'fields' => 'required|json',
            'requires_approval' => 'boolean',
            'approval_roles' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        $fields = json_decode($validated['fields'], true);
        if (empty($fields)) {
            return back()->withErrors(['fields' => 'Minimal satu field harus ditambahkan.'])->withInput();
        }

        $template->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'fields' => $fields,
            'requires_approval' => $request->boolean('requires_approval'),
            'approval_roles' => !empty($validated['approval_roles']) ? json_decode($validated['approval_roles'], true) : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.forms.index')
            ->with('success', 'Template form berhasil diperbarui.');
    }

    public function destroy(FormTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Template form berhasil dihapus.');
    }

    public function submissions(Request $request, FormTemplate $template)
    {
        $submissions = $template->submissions()
            ->with(['submitter', 'approver'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.forms.submissions', compact('template', 'submissions'));
    }

    public function showSubmission(FormSubmission $submission)
    {
        $submission->load(['template', 'submitter', 'approver', 'comments.user']);

        return view('admin.forms.submission-show', compact('submission'));
    }

    public function approve(FormSubmission $submission)
    {
        if ($submission->status !== 'submitted') {
            return back()->with('error', 'Submission ini tidak dapat disetujui.');
        }

        $submission->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Submission berhasil disetujui.');
    }

    public function reject(Request $request, FormSubmission $submission)
    {
        if ($submission->status !== 'submitted') {
            return back()->with('error', 'Submission ini tidak dapat ditolak.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $submission->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Submission berhasil ditolak.');
    }

    public function addComment(Request $request, FormSubmission $submission)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $submission->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function export(FormTemplate $template): StreamedResponse
    {
        $submissions = $template->submissions()
            ->with('submitter')
            ->where('status', '!=', 'draft')
            ->latest()
            ->get();

        $fields = $template->fields ?? [];
        $fieldNames = array_map(fn($f) => $f['label'] ?? $f['name'], $fields);

        $headers = array_merge(
            ['No', 'Pengirim', 'Status', 'Tanggal Submit'],
            $fieldNames,
            ['Disetujui Oleh', 'Tanggal Approval']
        );

        $filename = Str::slug($template->name) . '_submissions_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($submissions, $fields, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($submissions as $index => $submission) {
                $row = [
                    $index + 1,
                    $submission->submitter->name ?? '-',
                    $submission->status,
                    $submission->created_at->format('Y-m-d H:i'),
                ];

                foreach ($fields as $field) {
                    $row[] = $submission->data[$field['name']] ?? '-';
                }

                $row[] = $submission->approver->name ?? '-';
                $row[] = $submission->approved_at ? $submission->approved_at->format('Y-m-d H:i') : '-';

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
