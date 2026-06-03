<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
        'priority',
        'attachment',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Many-to-many: complaints can have many tags.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'complaint_tag');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Auto-generate a ticket number before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Complaint $complaint) {
            if (empty($complaint->ticket_number)) {
                $complaint->ticket_number = static::generateTicketNumber();
            }
        });
    }

    public static function generateTicketNumber(): string
    {
        $year = now()->year;
        $pattern = sprintf('CMP-%d-', $year);

        $lastNumber = static::query()
            ->where('ticket_number', 'like', $pattern . '%')
            ->get()
            ->map(fn (Complaint $complaint) => (int) substr($complaint->ticket_number, -5))
            ->max();

        $nextNumber = (int) ($lastNumber ?? 0) + 1;
        $ticketNumber = sprintf('CMP-%d-%05d', $year, $nextNumber);

        while (static::query()->where('ticket_number', $ticketNumber)->exists()) {
            $nextNumber++;
            $ticketNumber = sprintf('CMP-%d-%05d', $year, $nextNumber);
        }

        return $ticketNumber;
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'low'    => 'green',
            'medium' => 'yellow',
            'high'   => 'orange',
            'urgent' => 'red',
            default  => 'gray',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'     => 'gray',
            'open'        => 'blue',
            'in_progress' => 'yellow',
            'resolved'    => 'green',
            'closed'      => 'green',
            'rejected'    => 'red',
            default       => 'gray',
        };
    }
}
