<x-layout title="Reports & Analytics">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
    </x-slot>

    {{-- Date Range Filter --}}
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                <option value="">All</option>
                @foreach(['pending','open','in_progress','resolved','closed','rejected'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Generate</button>
    </form>

    {{-- Export Buttons --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @php $params = request()->only(['from','to','status']) @endphp
        <a href="{{ route('admin.reports.pdf',  $params) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
             Export PDF
        </a>
        <a href="{{ route('admin.reports.csv',  $params) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
             Export CSV
        </a>
        <a href="{{ route('admin.reports.xlsx', $params) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
             Export XLSX
        </a>
        <a href="{{ route('admin.reports.json', $params) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-gray-700 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
            { } Export JSON
        </a>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-8">
        @foreach([
            ['label'=>'Total Complaints',    'value'=>$stats['total'],            ],
            ['label'=>'Resolved',            'value'=>$stats['resolved'],          ],
            ['label'=>'Still Pending',       'value'=>$stats['pending'],           ],
            ['label'=>'Avg Response (hrs)',  'value'=>$stats['avg_response_hrs'],  ],
        ] as $s)
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 text-center">
                
                <div class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ $s['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- By Status --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4">By Status</h3>
            @foreach($byStatus as $status => $count)
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="capitalize text-gray-600">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="font-semibold">{{ $count }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-indigo-500"
                             style="width: {{ $stats['total'] > 0 ? ($count / $stats['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- By Priority --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4">By Priority</h3>
            @php $pColors = ['low'=>'bg-green-500','medium'=>'bg-yellow-500','high'=>'bg-orange-500','urgent'=>'bg-red-500'] @endphp
            @foreach($byPriority as $priority => $count)
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="capitalize text-gray-600">{{ $priority }}</span>
                        <span class="font-semibold">{{ $count }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100">
                        <div class="h-2 rounded-full {{ $pColors[$priority] ?? 'bg-gray-400' }}"
                             style="width: {{ $stats['total'] > 0 ? ($count / $stats['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- By Category --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4">By Category</h3>
            @foreach($byCategory as $category => $count)
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">{{ $category ?? 'Uncategorized' }}</span>
                        <span class="font-semibold">{{ $count }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-purple-500"
                             style="width: {{ $stats['total'] > 0 ? ($count / $stats['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout>
