<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ComplaintsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $complaints;

    public function __construct(Collection $complaints)
    {
        $this->complaints = $complaints;
    }

    public function collection()
    {
        return $this->complaints;
    }

    public function headings(): array
    {
        return [
            'Ticket Number',
            'Title',
            'User',
            'Category',
            'Status',
            'Priority',
            'Created At',
            'Resolved At',
        ];
    }

    public function map($complaint): array
    {
        return [
            $complaint->ticket_number,
            $complaint->title,
            $complaint->user->name,
            $complaint->category->name,
            ucfirst(str_replace('_', ' ', $complaint->status)),
            ucfirst($complaint->priority),
            $complaint->created_at->format('Y-m-d H:i'),
            $complaint->resolved_at?->format('Y-m-d H:i') ?? 'N/A',
        ];
    }
}
