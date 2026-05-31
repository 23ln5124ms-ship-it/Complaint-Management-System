@props(['status', 'type' => 'status'])

@php
    $colors = match($type) {
        'priority' => match($status) {
            'low'    => 'bg-green-100 text-green-700',
            'medium' => 'bg-yellow-100 text-yellow-700',
            'high'   => 'bg-orange-100 text-orange-700',
            'urgent' => 'bg-red-100 text-red-700',
            default  => 'bg-gray-100 text-gray-700',
        },
        default => match($status) {
            'pending'     => 'bg-gray-100 text-gray-700',
            'open'        => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-yellow-100 text-yellow-700',
            'resolved'    => 'bg-green-100 text-green-700',
            'closed'      => 'bg-emerald-100 text-emerald-700',
            'rejected'    => 'bg-red-100 text-red-700',
            default       => 'bg-gray-100 text-gray-600',
        },
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colors }}">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>
