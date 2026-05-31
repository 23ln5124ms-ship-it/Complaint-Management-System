<x-layout title="Admin Dashboard">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
    </x-slot>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 mb-8">
        @foreach([
            ['label' => 'Total',       'value' => $stats['total'],       'color' => 'bg-indigo-500'],
            ['label' => 'Pending',     'value' => $stats['pending'],     'color' => 'bg-gray-500'],
            ['label' => 'In Progress', 'value' => $stats['in_progress'], 'color' => 'bg-yellow-500'],
            ['label' => 'Resolved',    'value' => $stats['resolved'],    'color' => 'bg-green-500'],
            ['label' => 'Urgent',      'value' => $stats['urgent'],      'color' => 'bg-red-500'],
        ] as $stat)
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg {{ $stat['color'] }} flex items-center justify-center">
                        <span class="text-lg font-bold text-white">{{ $stat['value'] }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Recent Complaints Table --}}
        <div class="lg:col-span-2 rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-900">Recent Complaints</h2>
                <a href="{{ route('admin.complaints.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">View all →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentComplaints as $complaint)
                    <div class="flex items-center gap-4 px-6 py-3">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                               class="text-sm font-medium text-gray-900 hover:text-indigo-600 truncate block">
                                {{ $complaint->title }}
                            </a>
                            <p class="text-xs text-gray-500">
                                {{ $complaint->ticket_number }} · {{ $complaint->user->name }} · {{ $complaint->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <x-status-badge :status="$complaint->priority" type="priority" />
                            <x-status-badge :status="$complaint->status" />
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-400">No complaints yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Category Stats --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-900">By Category</h2>
            </div>
            <div class="divide-y divide-gray-100 px-6">
                @foreach($categoryStats as $cat)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $cat->color }}"></span>
                            <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $cat->complaints_count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layout>
