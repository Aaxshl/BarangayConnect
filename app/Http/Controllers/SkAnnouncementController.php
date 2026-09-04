<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Carbon\Carbon;

class SkAnnouncementController extends Controller {

    public function index(Request $request) {
        $query = Announcement::sk()->with('createdBy');

        if ($request->filled('type')) {
            $query->where('announcement_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query->latest('created_at')->paginate(10)->withQueryString();

        return view('sk.announcements.index', compact('announcements'));
    }

    public function create() {
        return view('sk.announcements.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'announcement_type' => 'required|in:' . implode(',', Announcement::TYPES),
            'body'              => 'required|string',
            'status'            => 'required|in:draft,published,scheduled',
            'published_at'      => 'nullable|required_if:status,scheduled|date|after:now',
            'image'             => 'nullable|image|max:2048',
        ]);

        $validated['created_by'] = auth()->id();

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'scheduled') {
            $validated['published_at'] = Carbon::parse($validated['published_at']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create($validated);

        $msg = $validated['status'] === 'scheduled'
            ? 'Announcement scheduled for ' . Carbon::parse($validated['published_at'])->format('M d, Y g:i A') . '.'
            : 'Youth announcement posted successfully.';

        return redirect()->route('sk.announcements.index')->with('success', $msg);
    }

    public function show(Announcement $announcement) {
        $this->authorizeSkAnnouncement($announcement);
        return view('sk.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement) {
        $this->authorizeSkAnnouncement($announcement);
        return view('sk.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement) {
        $this->authorizeSkAnnouncement($announcement);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'announcement_type' => 'required|in:' . implode(',', Announcement::TYPES),
            'body'              => 'required|string',
            'status'            => 'required|in:draft,published,scheduled,archived',
            'published_at'      => 'nullable|required_if:status,scheduled|date',
            'image'             => 'nullable|image|max:2048',
        ]);

        if ($validated['status'] === 'published' && !$announcement->published_at) {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'scheduled') {
            $validated['published_at'] = Carbon::parse($validated['published_at']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($validated);

        return redirect()->route('sk.announcements.show', $announcement)->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement) {
        $this->authorizeSkAnnouncement($announcement);

        $announcement->delete();
        return redirect()->route('sk.announcements.index')->with('success', 'Announcement has been removed.');
    }

    /**
     * Prevent SK Officials from accessing or altering Barangay announcements.
     */
    private function authorizeSkAnnouncement(Announcement $announcement): void {
        if (!$announcement->isSkAnnouncement()) {
            abort(403, 'Access denied. Barangay announcements can only be managed from the Main Barangay Admin Panel.');
        }
    }
}
