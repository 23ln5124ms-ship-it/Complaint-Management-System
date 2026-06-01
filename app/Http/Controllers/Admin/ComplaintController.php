<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Response;
use Illuminate\Http\Request;

class ComplaintController
{
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $complaints = $query->latest()->paginate(15);
        $categories = Category::where('is_active', true)->get();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'category', 'responses.user', 'tags']);
        $categories = Category::where('is_active', true)->get();

        return view('admin.complaints.show', compact('complaint', 'categories'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,open,in_progress,resolved,closed,rejected',
            'priority' => 'required|in:low,medium,high,urgent',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validated['status'] === 'resolved' && !$complaint->resolved_at) {
            $validated['resolved_at'] = now();
        }

        $complaint->update($validated);

        return redirect()->route('admin.complaints.show', $complaint)
                        ->with('success', 'Complaint updated successfully');
    }

    public function respond(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5',
            'is_internal' => 'boolean',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('responses', 'public');
        }

        Response::create([
            'complaint_id' => $complaint->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachment' => $attachment,
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return redirect()->route('admin.complaints.show', $complaint)
                        ->with('success', 'Response added successfully');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();

        return redirect()->route('admin.complaints.index')
                        ->with('success', 'Complaint deleted successfully');
    }
}
