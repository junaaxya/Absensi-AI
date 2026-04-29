<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::where('created_by', auth()->id())
            ->with(['category', 'assignee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = TicketCategory::where('is_active', true)->orderBy('name')->get();

        return view('tickets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        Ticket::create([
            'ticket_category_id' => $request->ticket_category_id,
            'created_by' => auth()->id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('tickets.index')
            ->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->created_by !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['category', 'assignee', 'creator']);

        $comments = $ticket->comments()
            ->where('is_internal', false)
            ->with('user')
            ->orderBy('created_at')
            ->get();

        return view('tickets.show', compact('ticket', 'comments'));
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        if ($ticket->created_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'is_internal' => false,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan.');
    }
}
