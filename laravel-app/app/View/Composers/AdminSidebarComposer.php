<?php

namespace App\View\Composers;

use App\Models\Izin;
use App\Models\Ticket;
use App\Models\FormSubmission;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class AdminSidebarComposer
{
    public function compose(View $view): void
    {
        $userId = auth()->id();
        $cacheKey = 'admin_sidebar_badges_' . $userId;

        $counts = Cache::remember($cacheKey, 30, function () use ($userId) {
            $lastSeenIzin = Cache::get("admin_last_seen_izin_{$userId}");
            $lastSeenTickets = Cache::get("admin_last_seen_tickets_{$userId}");
            $lastSeenForms = Cache::get("admin_last_seen_forms_{$userId}");
            $lastSeenEmployees = Cache::get("admin_last_seen_employees_{$userId}");

            $pendingIzin = Izin::where('status', 'pending')
                ->when($lastSeenIzin, fn($q) => $q->where('created_at', '>', $lastSeenIzin))
                ->count();

            $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])
                ->when($lastSeenTickets, fn($q) => $q->where('created_at', '>', $lastSeenTickets))
                ->count();

            $pendingForms = FormSubmission::where('status', 'pending')
                ->when($lastSeenForms, fn($q) => $q->where('created_at', '>', $lastSeenForms))
                ->count();

            $pendingApproval = User::where('is_approved', false)
                ->when($lastSeenEmployees, fn($q) => $q->where('created_at', '>', $lastSeenEmployees))
                ->count();

            return [
                'pending_izin' => $pendingIzin,
                'open_tickets' => $openTickets,
                'pending_forms' => $pendingForms,
                'pending_approval' => $pendingApproval,
            ];
        });

        $view->with('sidebarBadges', $counts);
    }

    /**
     * Mark a section as "seen" by the current admin.
     * Clears the badge for that section.
     */
    public static function markSeen(string $section): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        Cache::put("admin_last_seen_{$section}_{$userId}", now()->toDateTimeString(), 60 * 60 * 24 * 30);
        Cache::forget('admin_sidebar_badges_' . $userId);
    }
}
