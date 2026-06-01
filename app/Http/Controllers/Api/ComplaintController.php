<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\Category;
use Illuminate\Http\Request;

class ComplaintController
{
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'category', 'responses']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $complaints = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $complaints->items(),
            'pagination' => [
                'total' => $complaints->total(),
                'per_page' => $complaints->perPage(),
                'current_page' => $complaints->currentPage(),
                'last_page' => $complaints->lastPage(),
            ],
        ]);
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'category', 'responses.user', 'tags']);

        return response()->json([
            'success' => true,
            'data' => $complaint,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('complaints', 'public');
        }

        $complaint = new Complaint([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'priority' => $validated['priority'],
            'attachment' => $attachment,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        $complaint->ticket_number = $complaint->generateTicketNumber();
        $complaint->save();

        return response()->json([
            'success' => true,
            'message' => 'Complaint created successfully',
            'data' => $complaint,
        ], 201);
    }

    public function update(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:pending,open,in_progress,resolved,closed,rejected',
        ]);

        $complaint->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Complaint updated successfully',
            'data' => $complaint,
        ]);
    }

    public function destroy(Complaint $complaint)
    {
        $this->authorize('delete', $complaint);

        $complaint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint deleted successfully',
        ]);
    }
}
