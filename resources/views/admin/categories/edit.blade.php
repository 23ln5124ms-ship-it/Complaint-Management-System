<x-layout title="Edit Category">
    <x-slot name="header">
        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
            <p class="text-sm text-gray-500">Update the category details.</p>
        </div>
    </x-slot>

    <div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                       class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="4"
                          class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Color</label>
                    <input type="color" name="color" value="{{ old('color', $category->color) }}"
                           class="mt-2 h-12 w-full rounded-lg border border-gray-300 p-2 text-sm focus:outline-none @error('color') border-red-500 @enderror">
                    @error('color') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-slate-50 px-4 py-4">
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Active
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-layout>
