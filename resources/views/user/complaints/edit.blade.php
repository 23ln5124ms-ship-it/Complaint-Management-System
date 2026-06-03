<x-layout title="Edit Complaint">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Edit Complaint</h1>
        <p class="mt-1 text-sm text-gray-500">Update the details of your complaint before submitting the changes.</p>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('user.complaints.update', $complaint) }}"
              enctype="multipart/form-data"
              class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-5">
            @csrf
            @method('PUT')

            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category_id" required
                        class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $complaint->category_id) == $cat->id)>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $complaint->title) }}" required
                       placeholder="Brief summary of your complaint"
                       class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required
                          placeholder="Please describe your complaint in detail (minimum 20 characters)…"
                          class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $complaint->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                <div class="flex gap-3 flex-wrap">
                    @foreach(['low' => '🟢 Low', 'medium' => '🟡 Medium', 'high' => '🟠 High', 'urgent' => '🔴 Urgent'] as $value => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="priority" value="{{ $value }}"
                                   @checked(old('priority', $complaint->priority) === $value)
                                   class="text-indigo-600">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('priority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Tags --}}
            @if($tags->count())
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tags (optional)</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center gap-1.5 cursor-pointer rounded-full border border-gray-200 px-3 py-1 hover:bg-gray-50">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                   @checked(in_array($tag->id, old('tags', $complaint->tags->pluck('id')->toArray())))
                                   class="text-indigo-600 rounded">
                            <span class="text-xs text-gray-700">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Attachment --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment (optional)</label>
                <input type="file" name="attachment"
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @if($complaint->attachment)
                    <p class="mt-2 text-sm text-gray-500">Current file: <a href="{{ route('attachments.view', ['path' => $complaint->attachment]) }}" class="text-indigo-600 hover:text-indigo-500">View attachment</a></p>
                @endif
                <p class="mt-1 text-xs text-gray-400">PDF, JPG, PNG, DOC up to 5MB</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Save Changes
                </button>
                <a href="{{ route('user.complaints.show', $complaint) }}"
                   class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layout>
