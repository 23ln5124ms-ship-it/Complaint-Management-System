<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Response;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    // ─── Admin Dashboard ─────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'total'       => Complaint::count(),
            'pending'     => Complaint::byStatus('pending')->count(),
            'in_progress' => Complaint::byStatus('in_progress')->count(),
            'resolved'    => Complaint::byStatus('resolved')->count(),
            'urgent'      => Complaint::byPriority('urgent')->count(),
        ];

        $recentComplaints = Complaint::with(['user', 'category'])
            ->latest()
            ->take(10)
            ->get();

        $categoryStats = Category::withCount('complaints')->get();

        return view('admin.dashboard', compact('stats', 'recentComplaints', 'categoryStats'));
    }

    // ─── Admin: List all complaints ──────────────────────────────────────────

    public function index(Request $request)
    {
        $complaints = Complaint::with(['user', 'category', 'tags'])
            ->when($request->status, fn($q, $s) => $q->byStatus($s))
            ->when($request->priority, fn($q, $p) => $q->byPriority($p))
            ->when($request->category_id, fn($q, $c) => $q->where('category_id', $c))
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('ticket_number', 'like', "%{$s}%")
                        ->orWhere('title', 'like', "%{$s}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();

        return view('admin.complaints.index', compact('complaints', 'categories'));
    }

    // ─── Admin: View single complaint ────────────────────────────────────────

    public function show(Complaint $complaint)
    {
        $complaint->load(['user', 'category', 'tags', 'responses.user']);
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.complaints.show', compact('complaint', 'categories', 'tags'));
    }

    // ─── Admin: Update status & priority ─────────────────────────────────────

    public function update(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:pending,open,in_progress,resolved,closed,rejected'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
        ]);

        if (in_array($data['status'], ['resolved', 'closed']) && ! $complaint->resolved_at) {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);
        $complaint->tags()->sync($data['tags'] ?? []);

        return back()->with('success', 'Complaint updated.');
    }

    // ─── Admin: Delete complaint ──────────────────────────────────────────────

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return redirect()->route('admin.complaints.index')
            ->with('success', 'Complaint deleted.');
    }

    // ─── Admin: Add response ─────────────────────────────────────────────────

    public function respond(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message'     => ['required', 'string', 'min:5'],
            'is_internal' => ['boolean'],
            'attachment'  => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('complaints/responses', 'public');
        }

        Response::create([
            'complaint_id' => $complaint->id,
            'user_id'      => auth()->id(),
            'message'      => $request->message,
            'attachment'   => $path,
            'is_internal'  => $request->boolean('is_internal'),
        ]);

        // Auto-update status to in_progress when admin first responds
        if ($complaint->status === 'pending') {
            $complaint->update(['status' => 'in_progress']);
        }

        // TODO: Notify complaint owner via email

        return back()->with('success', 'Response posted.');
    }
}
