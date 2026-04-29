<?php

namespace App\Http\Controllers;

use App\Models\FormTemplate;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $templates = FormTemplate::active()
            ->withCount('submissions')
            ->orderBy('name')
            ->get();

        return view('forms.index', compact('templates'));
    }

    public function show(FormTemplate $template)
    {
        if (!$template->is_active) {
            abort(404);
        }

        return view('forms.show', compact('template'));
    }

    public function store(Request $request, FormTemplate $template)
    {
        if (!$template->is_active) {
            abort(404);
        }

        $fields = $template->fields ?? [];
        $rules = [];
        $labels = [];

        foreach ($fields as $field) {
            $fieldRules = [];

            if (!empty($field['required'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field['type'] ?? 'text') {
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:10240';
                    break;
                case 'checkbox':
                    $fieldRules = ['nullable'];
                    break;
                default:
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:5000';
                    break;
            }

            $rules['field_' . $field['name']] = $fieldRules;
            $labels['field_' . $field['name']] = $field['label'] ?? $field['name'];
        }

        $validated = $request->validate($rules, [], $labels);

        $data = [];
        $attachments = [];

        foreach ($fields as $field) {
            $inputKey = 'field_' . $field['name'];

            if (($field['type'] ?? 'text') === 'file' && $request->hasFile($inputKey)) {
                $path = $request->file($inputKey)->store('form-uploads', 'public');
                $attachments[$field['name']] = $path;
                $data[$field['name']] = basename($path);
            } elseif (($field['type'] ?? 'text') === 'checkbox') {
                $data[$field['name']] = $request->has($inputKey) ? 'Ya' : 'Tidak';
            } else {
                $data[$field['name']] = $validated[$inputKey] ?? null;
            }
        }

        $submission = FormSubmission::create([
            'form_template_id' => $template->id,
            'submitted_by' => auth()->id(),
            'data' => $data,
            'status' => 'submitted',
            'attachments' => !empty($attachments) ? $attachments : null,
        ]);

        return redirect()->route('forms.submission.show', $submission)
            ->with('success', 'Form berhasil dikirim.');
    }

    public function mySubmissions(Request $request)
    {
        $submissions = FormSubmission::with('template')
            ->where('submitted_by', auth()->id())
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('forms.submissions', compact('submissions'));
    }

    public function showSubmission(FormSubmission $submission)
    {
        if ($submission->submitted_by !== auth()->id()) {
            abort(403);
        }

        $submission->load(['template', 'submitter', 'approver', 'comments.user']);

        return view('forms.submission-show', compact('submission'));
    }

    public function addComment(Request $request, FormSubmission $submission)
    {
        if ($submission->submitted_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $submission->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
