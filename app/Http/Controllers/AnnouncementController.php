<?php
namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AnnouncementController extends Controller {
    public function index(Request $request) {
        $query = Announcement::barangay()->with('createdBy');

        $query->when($request->search, function ($q, $s) {
            $q->where(function ($sub) use ($s) {
                $sub->where('title', 'like', "%{$s}%")
                    ->orWhere('body', 'like', "%{$s}%");
            });
        });

        $query->when($request->type, fn($q, $t) => $q->where('announcement_type', $t));
        $query->when($request->status, fn($q, $s) => $q->where('status', $s));

        $announcements = $query->latest()->paginate(12)->withQueryString();

        $counts = [
            'all'       => Announcement::barangay()->count(),
            'published' => Announcement::barangay()->where('status', 'published')->count(),
            'scheduled' => Announcement::barangay()->where('status', 'scheduled')->count(),
            'draft'     => Announcement::barangay()->where('status', 'draft')->count(),
            'archived'  => Announcement::barangay()->where('status', 'archived')->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'counts'));
    }

    public function create() {
        return view('admin.announcements.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'body'              => 'required|string',
            'announcement_type' => 'required|in:' . implode(',', Announcement::TYPES),
            'image'             => 'nullable|image|max:4096',
            'action'            => 'required|in:publish_now,schedule,draft',
            'scheduled_at'      => 'required_if:action,schedule|nullable|date',
        ]);

        $validated['created_by'] = auth()->id();

        // Handle publishing status and timestamp
        if ($validated['action'] === 'publish_now') {
            $validated['status']       = 'published';
            $validated['published_at'] = now();
        } elseif ($validated['action'] === 'schedule') {
            $validated['status']       = 'scheduled';
            $validated['published_at'] = Carbon::parse($validated['scheduled_at']);
        } else {
            $validated['status']       = 'draft';
            $validated['published_at'] = null;
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        unset($validated['action'], $validated['scheduled_at']);

        $announcement = Announcement::create($validated);

        $msg = match ($announcement->status) {
            'published' => 'Announcement published immediately.',
            'scheduled' => 'Announcement scheduled for ' . $announcement->published_at->format('M d, Y h:i A') . '.',
            default     => 'Announcement saved as draft.',
        };

        return redirect()->route('admin.announcements.index')->with('success', $msg);
    }

    public function show(Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);
        $announcement->load('createdBy');
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'body'              => 'required|string',
            'announcement_type' => 'required|in:' . implode(',', Announcement::TYPES),
            'image'             => 'nullable|image|max:4096',
            'action'            => 'nullable|in:publish_now,schedule,draft,keep',
            'scheduled_at'      => 'nullable|date',
        ]);

        $action = $request->input('action', 'keep');

        if ($action === 'publish_now') {
            $validated['status']       = 'published';
            $validated['published_at'] = now();
            $validated['archived_at']  = null;
        } elseif ($action === 'schedule' && $request->filled('scheduled_at')) {
            $validated['status']       = 'scheduled';
            $validated['published_at'] = Carbon::parse($request->scheduled_at);
            $validated['archived_at']  = null;
        } elseif ($action === 'draft') {
            $validated['status']       = 'draft';
            $validated['published_at'] = null;
            $validated['archived_at']  = null;
        }

        if ($request->hasFile('image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        if ($request->boolean('remove_image')) {
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = null;
        }

        unset($validated['action'], $validated['scheduled_at'], $validated['remove_image']);

        $announcement->update($validated);
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);

        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }

    public function publish(Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);

        $announcement->update([
            'status'       => 'published',
            'published_at' => now(),
            'archived_at'  => null,
        ]);
        return back()->with('success', 'Announcement is now published live.');
    }

    public function schedule(Request $request, Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);

        $request->validate([
            'scheduled_at' => 'required|date',
        ]);

        $announcement->update([
            'status'       => 'scheduled',
            'published_at' => Carbon::parse($request->scheduled_at),
            'archived_at'  => null,
        ]);

        return back()->with('success', 'Announcement scheduled for ' . $announcement->published_at->format('M d, Y h:i A') . '.');
    }

    public function archive(Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);

        $announcement->update([
            'status'      => 'archived',
            'archived_at' => now(),
        ]);
        return back()->with('success', 'Announcement archived.');
    }

    public function revertToDraft(Announcement $announcement) {
        $this->authorizeBarangayAnnouncement($announcement);

        $announcement->update([
            'status'       => 'draft',
            'published_at' => null,
            'archived_at'  => null,
        ]);
        return back()->with('success', 'Announcement moved back to drafts.');
    }

    /**
     * Prevent Barangay Officials from accessing or altering SK announcements.
     */
    private function authorizeBarangayAnnouncement(Announcement $announcement): void {
        if ($announcement->isSkAnnouncement()) {
            abort(403, 'Access denied. Sangguniang Kabataan (SK) announcements can only be managed from the SK Portal.');
        }
    }
}
