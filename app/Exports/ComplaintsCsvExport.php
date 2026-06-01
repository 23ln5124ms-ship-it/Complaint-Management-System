<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class ComplaintsCsvExport
{
    protected $complaints;

    public function __construct(Collection $complaints)
    {
        $this->complaints = $complaints;
    }

    public function generate()
    {
        $csv = "Ticket Number,Title,User,Category,Status,Priority,Created At,Resolved At\n";

        foreach ($this->complaints as $complaint) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $complaint->ticket_number,
                '"' . $complaint->title . '"',
                $complaint->user->name,
                $complaint->category->name,
                ucfirst(str_replace('_', ' ', $complaint->status)),
                ucfirst($complaint->priority),
                $complaint->created_at->format('Y-m-d H:i'),
                $complaint->resolved_at?->format('Y-m-d H:i') ?? 'N/A'
            );
        }

        return $csv;
    }
}
