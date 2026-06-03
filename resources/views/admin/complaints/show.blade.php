<x-layout title="Complaint {{ $complaint->ticket_number }}">
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $complaint->title }}</h1>
                <p class="text-sm text-gray-500">Ticket {{ $complaint->ticket_number }} · {{ $complaint->user->name }} · {{ $complaint->created_at->format('M d, Y') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-status-badge :status="$complaint->priority" type="priority" />
                <x-status-badge :status="$complaint->status" />
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[2fr_1fr]">
        <div class="space-y-6">
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Complaint Details</h2>
                        <p class="mt-2 text-sm text-gray-500">Review and manage the complaint.</p>
                    </div>
                    <div class="inline-flex gap-2">
                        <a href="{{ route('admin.complaints.index') }}" class="rounded-lg border border-gray-300 bg-slate-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-slate-100">Back to list</a>
                    </div>
                </div>

                <div class="mt-6 space-y-6 text-sm text-gray-700">
                    <div>
                        <h3 class="font-semibold text-gray-900">Description</h3>
                        <p class="mt-3 whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Category</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $complaint->category->name }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Submitted by</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $complaint->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->user->email }}</p>
                        </div>
                    </div>

                    @if($complaint->tags->isNotEmpty())
                        <div>
                            <h3 class="font-semibold text-gray-900">Tags</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($complaint->tags as $tag)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($complaint->attachment)
                        <div>
                            <h3 class="font-semibold text-gray-900">Attachment</h3>
                            <a href="{{ route('attachments.view', ['path' => $complaint->attachment]) }}" target="_blank" rel="noreferrer noopener"
                               class="mt-2 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View attachment
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Responses</h2>
                <div class="mt-4 space-y-4">
                    @forelse($complaint->responses as $response)
                        <div class="rounded-2xl border border-gray-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $response->user->name }} <span class="text-xs text-gray-500">{{ $response->user->isAdmin() ? 'Admin' : 'User reply' }}</span></p>
                                    <p class="text-xs text-gray-500">{{ $response->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($response->attachment)
                                    <a href="{{ route('attachments.view', ['path' => $response->attachment]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500" target="_blank" rel="noreferrer noopener">View</a>
                                @endif
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-700">{{ $response->message }}</p>
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-gray-200 bg-slate-50 p-6 text-sm text-gray-500">No responses yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('admin.complaints.index') }}" class="block rounded-lg border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-white">Back to complaints</a>
                    <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Delete this complaint?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700">Delete Complaint</button>
                    </form>
                </div>
            </section>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Update Complaint</h2>
                <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @foreach(['pending','open','in_progress','resolved','closed','rejected'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $complaint->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @foreach(['low','medium','high','urgent'] as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', $complaint->priority) === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $complaint->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Post Response</h2>
                <form action="{{ route('admin.complaints.respond', $complaint) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" rows="4" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('message') }}</textarea>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" name="is_internal" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"> Internal note
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Attachment</label>
                        <input type="file" name="attachment" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Post Response</button>
                </form>
            </section>
        </aside>
    </div>
</x-layout>
