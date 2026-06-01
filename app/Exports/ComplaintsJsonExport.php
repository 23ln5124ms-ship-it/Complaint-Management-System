<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class ComplaintsJsonExport
{
    protected $complaints;

    public function __construct(Collection $complaints)
    {
        $this->complaints = $complaints;
    }

    public function generate()
    {
        $data = $this->complaints->map(function ($complaint) {
            return [
                'ticket_number' => $complaint->ticket_number,
                'title' => $complaint->title,
                'user' => $complaint->user->name,
                'category' => $complaint->category->name,
                'status' => $complaint->status,
                'priority' => $complaint->priority,
                'description' => $complaint->description,
                'created_at' => $complaint->created_at->toIso8601String(),
                'resolved_at' => $complaint->resolved_at?->toIso8601String(),
            ];
        })->all();

        return json_encode(['complaints' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
