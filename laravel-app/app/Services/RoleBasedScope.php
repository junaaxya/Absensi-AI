<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class RoleBasedScope
{
    /**
     * Scope attendance queries based on user role.
     * Filters the 'user' relationship on the Attendance model.
     */
    public static function scopeAttendance(Builder $query, User $user): Builder
    {
        if ($user->hasRole(['Direktur', 'Vice President'])) {
            // Full access - no filter
            return $query;
        }

        if ($user->hasRole('Manager')) {
            // Department-scoped: only attendance of users in same department
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        if ($user->hasRole(['Supervisor', 'Team Leader'])) {
            // Team-scoped: for now, same as department since no team table exists
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        // Staf/Magang: self only
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope izin (leave/absence request) queries based on user role.
     */
    public static function scopeIzin(Builder $query, User $user): Builder
    {
        if ($user->hasRole(['Direktur', 'Vice President'])) {
            return $query;
        }

        if ($user->hasRole('Manager')) {
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        if ($user->hasRole(['Supervisor', 'Team Leader'])) {
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        return $query->where('user_id', $user->id);
    }

    /**
     * Scope user count queries for dashboard.
     * Returns a query builder for counting employees visible to this user.
     */
    public static function scopeUsers(Builder $query, User $user): Builder
    {
        if ($user->hasRole(['Direktur', 'Vice President'])) {
            return $query;
        }

        if ($user->hasRole(['Manager', 'Supervisor', 'Team Leader'])) {
            return $query->where('department_id', $user->department_id);
        }

        return $query->where('id', $user->id);
    }
}
