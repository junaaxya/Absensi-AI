<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['category', 'creator', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('ticket_category_id', $request->category);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('sla_breach')) {
            $query->where(function ($q) {
                $q->where('sla_response_met', false)
                    ->orWhere('sla_resolution_met', false)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('sla_response_met')
                            ->where('sla_response_deadline', '<', now());
                    })
                    ->orWhere(function ($q2) {
                        $q2->whereNull('sla_resolution_met')
                            ->where('sla_resolution_deadline', '<', now());
                    });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('creator', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $categories = TicketCategory::where('is_active', true)->get();
        $staff = User::role(['Direktur', 'Vice President', 'Manager', 'Supervisor'])->orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'categories', 'staff'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['category', 'creator', 'assignee']);

        $comments = $ticket->comments()
            ->with('user')
            ->orderBy('created_at')
            ->get();

        $staff = User::role(['Direktur', 'Vice President', 'Manager', 'Supervisor'])->orderBy('name')->get();

        return view('admin.tickets.show', compact('ticket', 'comments', 'staff'));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);

        return redirect()->back()->with('success', 'Tiket berhasil di-assign.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,waiting,resolved,closed,cancelled',
            'resolution' => 'nullable|required_if:status,resolved|string|max:5000',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'resolved') {
            $data['resolution'] = $request->resolution;
            $data['resolved_at'] = now();
            $data['sla_resolution_met'] = $ticket->sla_resolution_deadline
                ? now()->lessThanOrEqualTo($ticket->sla_resolution_deadline)
                : null;
        }

        if ($request->status === 'closed') {
            $data['closed_at'] = now();
        }

        $ticket->update($data);

        return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string|max:5000',
            'is_internal' => 'boolean',
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        if (!$request->boolean('is_internal', false) && $ticket->created_by !== auth()->id()) {
            if (is_null($ticket->sla_response_met)) {
                $ticket->update([
                    'sla_response_met' => $ticket->sla_response_deadline
                        ? now()->lessThanOrEqualTo($ticket->sla_response_deadline)
                        : null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function categories()
    {
        $categories = TicketCategory::withCount('tickets')->orderBy('name')->get();

        return view('admin.tickets.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:ticket_categories,code',
            'description' => 'nullable|string|max:1000',
            'default_priority' => 'required|in:low,medium,high,critical',
            'sla_response_hours' => 'required|integer|min:1',
            'sla_resolution_hours' => 'required|integer|min:1',
        ]);

        TicketCategory::create($request->only([
            'name', 'code', 'description', 'default_priority',
            'sla_response_hours', 'sla_resolution_hours',
        ]));

        return redirect()->route('admin.tickets.categories')
            ->with('success', 'Kategori tiket berhasil dibuat.');
    }

    public function updateCategory(Request $request, TicketCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:ticket_categories,code,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'default_priority' => 'required|in:low,medium,high,critical',
            'sla_response_hours' => 'required|integer|min:1',
            'sla_resolution_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $category->update($request->only([
            'name', 'code', 'description', 'default_priority',
            'sla_response_hours', 'sla_resolution_hours', 'is_active',
        ]));

        return redirect()->route('admin.tickets.categories')
            ->with('success', 'Kategori tiket berhasil diperbarui.');
    }

    public function destroyCategory(TicketCategory $category)
    {
        if ($category->tickets()->exists()) {
            return redirect()->route('admin.tickets.categories')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki tiket.');
        }

        $category->delete();

        return redirect()->route('admin.tickets.categories')
            ->with('success', 'Kategori tiket berhasil dihapus.');
    }

    public function dashboard()
    {
        $openCount = Ticket::where('status', 'open')->count();
        $inProgressCount = Ticket::where('status', 'in_progress')->count();
        $waitingCount = Ticket::where('status', 'waiting')->count();
        $resolvedCount = Ticket::where('status', 'resolved')->count();
        $closedCount = Ticket::where('status', 'closed')->count();

        $totalResolved = Ticket::whereNotNull('resolved_at')->count();
        $slaResponseMet = Ticket::where('sla_response_met', true)->count();
        $slaResolutionMet = Ticket::where('sla_resolution_met', true)->count();
        $totalWithSlaResponse = Ticket::whereNotNull('sla_response_met')->count();
        $totalWithSlaResolution = Ticket::whereNotNull('sla_resolution_met')->count();

        $slaResponseCompliance = $totalWithSlaResponse > 0
            ? round(($slaResponseMet / $totalWithSlaResponse) * 100, 1)
            : 100;

        $slaResolutionCompliance = $totalWithSlaResolution > 0
            ? round(($slaResolutionMet / $totalWithSlaResolution) * 100, 1)
            : 100;

        $avgResolutionHours = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
        $avgResolutionHours = $avgResolutionHours ? round($avgResolutionHours, 1) : 0;

        $ticketsByCategory = TicketCategory::withCount(['tickets' => function ($q) {
            $q->whereIn('status', ['open', 'in_progress', 'waiting']);
        }])->get();

        $recentTickets = Ticket::with(['category', 'creator', 'assignee'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('admin.tickets.dashboard', compact(
            'openCount', 'inProgressCount', 'waitingCount', 'resolvedCount', 'closedCount',
            'slaResponseCompliance', 'slaResolutionCompliance', 'avgResolutionHours',
            'ticketsByCategory', 'recentTickets'
        ));
    }
}
