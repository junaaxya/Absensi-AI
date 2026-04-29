<?php

namespace App\Models;

use App\Traits\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'ticket_number',
        'ticket_category_id',
        'created_by',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'resolution',
        'resolved_at',
        'closed_at',
        'sla_response_deadline',
        'sla_resolution_deadline',
        'sla_response_met',
        'sla_resolution_met',
        'attachments',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_response_deadline' => 'datetime',
        'sla_resolution_deadline' => 'datetime',
        'sla_response_met' => 'boolean',
        'sla_resolution_met' => 'boolean',
        'attachments' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $today = now()->format('Ymd');
                $lastTicket = static::where('ticket_number', 'like', "TKT-{$today}-%")
                    ->orderByDesc('ticket_number')
                    ->first();

                if ($lastTicket) {
                    $lastSeq = (int) substr($lastTicket->ticket_number, -4);
                    $nextSeq = $lastSeq + 1;
                } else {
                    $nextSeq = 1;
                }

                $ticket->ticket_number = sprintf('TKT-%s-%04d', $today, $nextSeq);
            }

            if ($ticket->ticket_category_id) {
                $category = TicketCategory::find($ticket->ticket_category_id);
                if ($category) {
                    if (empty($ticket->sla_response_deadline)) {
                        $ticket->sla_response_deadline = now()->addHours($category->sla_response_hours);
                    }
                    if (empty($ticket->sla_resolution_deadline)) {
                        $ticket->sla_resolution_deadline = now()->addHours($category->sla_resolution_hours);
                    }
                }
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function getSlaResponseStatusAttribute(): ?string
    {
        if (!$this->sla_response_deadline) {
            return null;
        }

        if ($this->sla_response_met === true) {
            return 'green';
        }

        if ($this->sla_response_met === false) {
            return 'red';
        }

        $now = now();
        if ($now->greaterThan($this->sla_response_deadline)) {
            return 'red';
        }

        $hoursRemaining = $now->diffInMinutes($this->sla_response_deadline) / 60;
        return $hoursRemaining <= 2 ? 'yellow' : 'green';
    }

    public function getSlaResolutionStatusAttribute(): ?string
    {
        if (!$this->sla_resolution_deadline) {
            return null;
        }

        if ($this->sla_resolution_met === true) {
            return 'green';
        }

        if ($this->sla_resolution_met === false) {
            return 'red';
        }

        $now = now();
        if ($now->greaterThan($this->sla_resolution_deadline)) {
            return 'red';
        }

        $hoursRemaining = $now->diffInMinutes($this->sla_resolution_deadline) / 60;
        return $hoursRemaining <= 2 ? 'yellow' : 'green';
    }
}
