<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ResponseController
{
    public function index(Complaint $complaint)
    {
        $responses = $complaint->responses()
                              ->with('user')
                              ->latest()
                              ->get();

        return response()->json([
            'success' => true,
            'data' => $responses,
        ]);
    }

    public function store(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:5',
            'attachment' => 'nullable|file|max:5120',
            'is_internal' => 'boolean',
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('responses', 'public');
        }

        $response = $complaint->responses()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'attachment' => $attachment,
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Response created successfully',
            'data' => $response->load('user'),
        ], 201);
    }
}
