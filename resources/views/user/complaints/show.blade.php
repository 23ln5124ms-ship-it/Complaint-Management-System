<x-layout title="Complaint {{ $complaint->ticket_number }}">
    <x-slot name="header">
        <div class="space-y-2">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $complaint->title }}</h1>
                    <p class="text-sm text-gray-500">Complaint {{ $complaint->ticket_number }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-status-badge :status="$complaint->priority" type="priority" />
                    <x-status-badge :status="$complaint->status" />
                </div>
            </div>
            <p class="text-sm text-gray-500">Submitted {{ $complaint->created_at->diffForHumans() }} · Category: {{ $complaint->category->name }}</p>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">
        <section class="space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Details</h2>
                <div class="mt-4 space-y-4 text-sm text-gray-700">
                    <div>
                        <h3 class="font-medium text-gray-900">Description</h3>
                        <p class="mt-2 whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Submitted by</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $complaint->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $complaint->user->email }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-gray-500">Updated</p>
                            <p class="mt-2 font-medium text-gray-900">{{ $complaint->updated_at->diffForHumans() }}</p>
                            <p class="text-xs text-gray-400">{{ $complaint->updated_at->format('M j, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($complaint->tags->isNotEmpty())
                        <div>
                            <h3 class="font-medium text-gray-900">Tags</h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($complaint->tags as $tag)
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($complaint->attachment)
                        <div>
                            <h3 class="font-medium text-gray-900">Attachment</h3>
                            <a href="{{ route('attachments.view', ['path' => $complaint->attachment]) }}"
                               class="mt-2 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-500"
                               target="_blank" rel="noreferrer noopener">
                                View attachment
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7v7m0-7L10 14m0 0H3v7h7m-7-7l11-11"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900">Responses</h2>
                    <span class="text-sm text-gray-500">{{ $complaint->responses->count() }} reply(ies)</span>
                </div>

                <div class="mt-4 space-y-4">
                    @forelse($complaint->responses as $response)
                        <div class="rounded-2xl border border-gray-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $response->user->name }} <span class="text-xs text-gray-500">({{ $response->user->isAdmin() ? 'Admin' : 'You' }})</span></p>
                                    <p class="text-xs text-gray-400">{{ $response->created_at->format('M j, Y h:i A') }}</p>
                                </div>
                                @if($response->attachment)
                                    <a href="{{ route('attachments.view', ['path' => $response->attachment]) }}"
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                                       target="_blank" rel="noreferrer noopener">View</a>
                                @endif
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-700">{{ $response->message }}</p>
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-gray-200 bg-slate-50 p-6 text-sm text-gray-500">No responses have been posted yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Add a reply</h2>
                <form method="POST" action="{{ route('user.complaints.reply', $complaint) }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" rows="4" required
                                  class="mt-2 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                        <input type="file" name="attachment"
                               class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                        Send Reply
                    </button>
                </form>
            </div>
        </section>

        <aside class="space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('user.complaints.index') }}"
                       class="block rounded-lg border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 hover:border-indigo-300 hover:bg-white">
                        Back to complaints
                    </a>

                    @if($complaint->status === 'pending')
                        <a href="{{ route('user.complaints.edit', $complaint) }}"
                           class="block rounded-lg border border-gray-200 bg-slate-50 px-4 py-3 text-sm font-medium text-gray-700 hover:border-indigo-300 hover:bg-white">
                            Edit complaint
                        </a>
                    @endif
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Summary</h2>
                <dl class="mt-4 space-y-3 text-sm text-gray-700">
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Category</dt>
                        <dd>{{ $complaint->category->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Priority</dt>
                        <dd class="text-right">{{ ucfirst($complaint->priority) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Status</dt>
                        <dd class="text-right">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-medium text-gray-500">Created</dt>
                        <dd class="text-right">{{ $complaint->created_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </aside>
    </div>
</x-layout>
