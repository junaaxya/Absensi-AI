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
        $counts = Cache::remember('admin_sidebar_badges_' . auth()->id(), 60, function () {
            return [
                'pending_izin' => Izin::where('status', 'pending')->count(),
                'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
                'pending_forms' => FormSubmission::where('status', 'pending')->count(),
                'pending_approval' => User::where('is_approved', false)->count(),
            ];
        });

        $view->with('sidebarBadges', $counts);
    }
}
