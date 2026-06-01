<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Response;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    // ─── User: List own complaints ────────────────────────────────────────────

    public function index(Request $request)
    {
        $complaints = Complaint::with(['category', 'responses'])
            ->forUser(auth()->id())
            ->when($request->status, fn($q, $s) => $q->byStatus($s))
            ->when($request->priority, fn($q, $p) => $q->byPriority($p))
            ->latest()
            ->paginate(10);

        return view('user.complaints.index', compact('complaints'));
    }

    // ─── User: Show single complaint ─────────────────────────────────────────

    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);

        $complaint->load(['category', 'tags', 'responses.user']);

        return view('user.complaints.show', compact('complaint'));
    }

    // ─── User: Create form ───────────────────────────────────────────────────

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        return view('user.complaints.create', compact('categories', 'tags'));
    }

    // ─── User: Store new complaint ───────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'attachment'  => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('complaints/attachments', 'public');
        }

        $complaint = Complaint::create([
            ...$data,
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]);

        if (! empty($data['tags'])) {
            $complaint->tags()->sync($data['tags']);
        }

        // TODO: dispatch ComplaintSubmitted notification/mail

        return redirect()->route('user.complaints.show', $complaint)
            ->with('success', "Complaint #{$complaint->ticket_number} submitted successfully!");
    }

    // ─── User: Edit own pending complaint ────────────────────────────────────

    public function edit(Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        return view('user.complaints.edit', compact('complaint', 'categories', 'tags'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $this->authorize('update', $complaint);

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'attachment'  => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($complaint->attachment) {
                Storage::disk('public')->delete($complaint->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('complaints/attachments', 'public');
        }

        $complaint->update($data);
        $complaint->tags()->sync($data['tags'] ?? []);

        return redirect()->route('user.complaints.show', $complaint)
            ->with('success', 'Complaint updated successfully.');
    }

    // ─── User: Delete own pending complaint ──────────────────────────────────

    public function destroy(Complaint $complaint)
    {
        $this->authorize('delete', $complaint);

        if ($complaint->attachment) {
            Storage::disk('public')->delete($complaint->attachment);
        }

        $complaint->delete();

        return redirect()->route('user.complaints.index')
            ->with('success', 'Complaint deleted.');
    }

    // ─── User: Add a reply to their own complaint ────────────────────────────

    public function reply(Request $request, Complaint $complaint)
    {
        $this->authorize('view', $complaint);

        $request->validate([
            'message'    => ['required', 'string', 'min:5'],
            'attachment' => ['nullable', 'file', 'max:5120'],
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
            'is_internal'  => false,
        ]);

        return back()->with('success', 'Reply sent.');
    }
}
