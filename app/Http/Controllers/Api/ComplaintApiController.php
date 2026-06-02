<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Response as ComplaintResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComplaintApiController extends Controller
{
    // GET /api/complaints
    public function index(Request $request): JsonResponse
    {
        $complaints = Complaint::with(['user:id,name', 'category:id,name', 'tags'])
            ->when(! auth()->user()->isAdmin(), fn($q) => $q->forUser(auth()->id()))
            ->when($request->status, fn($q, $s) => $q->byStatus($s))
            ->when($request->priority, fn($q, $p) => $q->byPriority($p))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json($complaints);
    }

    // GET /api/complaints/{complaint}
    public function show(Complaint $complaint): JsonResponse
    {
        if (! auth()->user()->isAdmin() && $complaint->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $complaint->load(['user:id,name,email', 'category', 'tags', 'responses.user:id,name,role']);

        return response()->json(['data' => $complaint]);
    }

    // POST /api/complaints
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
        ]);

        $complaint = Complaint::create([
            ...$data,
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]);

        return response()->json([
            'message' => 'Complaint submitted.',
            'data'    => $complaint->load('category'),
        ], 201);
    }

    // PUT/PATCH /api/complaints/{complaint}
    public function update(Request $request, Complaint $complaint): JsonResponse
    {
        // Users can only update their own pending complaints
        if (! auth()->user()->isAdmin()) {
            if ($complaint->user_id !== auth()->id() || $complaint->status !== 'pending') {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $rules = auth()->user()->isAdmin()
            ? ['status' => 'in:pending,open,in_progress,resolved,closed,rejected', 'priority' => 'in:low,medium,high,urgent']
            : ['title' => 'string|max:255', 'description' => 'string|min:20', 'priority' => 'in:low,medium,high,urgent'];

        $complaint->update($request->validate($rules));

        return response()->json(['message' => 'Complaint updated.', 'data' => $complaint]);
    }

    // DELETE /api/complaints/{complaint}
    public function destroy(Complaint $complaint): JsonResponse
    {
        if (! auth()->user()->isAdmin() && $complaint->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $complaint->delete();

        return response()->json(['message' => 'Complaint deleted.']);
    }

    // ─── Responses ────────────────────────────────────────────────────────────

    // GET /api/complaints/{complaint}/responses
    public function responses(Complaint $complaint): JsonResponse
    {
        $responses = $complaint->responses()
            ->with('user:id,name,role')
            ->when(! auth()->user()->isAdmin(), fn($q) => $q->where('is_internal', false))
            ->get();

        return response()->json(['data' => $responses]);
    }

    // POST /api/complaints/{complaint}/responses
    public function storeResponse(Request $request, Complaint $complaint): JsonResponse
    {
        $data = $request->validate([
            'message'     => ['required', 'string', 'min:5'],
            'is_internal' => ['boolean'],
        ]);

        if (! auth()->user()->isAdmin() && $complaint->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $response = ComplaintResponse::create([
            ...$data,
            'complaint_id' => $complaint->id,
            'user_id'      => auth()->id(),
            'is_internal'  => auth()->user()->isAdmin() && ($data['is_internal'] ?? false),
        ]);

        return response()->json(['message' => 'Response posted.', 'data' => $response], 201);
    }

    // GET /api/categories
    public function categories(): JsonResponse
    {
        return response()->json(['data' => Category::where('is_active', true)->get()]);
    }
}
