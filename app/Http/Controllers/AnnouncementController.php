<?php
namespace App\Http\Controllers;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller {
    public function index() {
        $announcements = Announcement::with('createdBy')->latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }
    public function create() { return view('admin.announcements.create'); }
    public function store(Request $request) {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'body'              => 'required|string',
            'announcement_type' => 'required|in:'.implode(',',Announcement::TYPES),
            'image'             => 'nullable|image|max:4096',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements','public');
        }
        Announcement::create($validated);
        return redirect()->route('admin.announcements.index')->with('success','Announcement created.');
    }
    public function show(Announcement $announcement) {
        return view('admin.announcements.show', compact('announcement'));
    }
    public function edit(Announcement $announcement) {
        return view('admin.announcements.edit', compact('announcement'));
    }
    public function update(Request $request, Announcement $announcement) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
            'announcement_type' => 'required|in:'.implode(',',Announcement::TYPES),
            'image'             => 'nullable|image|max:4096',
        ]);
        if ($request->hasFile('image')) {
            if ($announcement->image) Storage::disk('public')->delete($announcement->image);
            $validated['image'] = $request->file('image')->store('announcements','public');
        }
        $announcement->update($validated);
        return redirect()->route('admin.announcements.index')->with('success','Announcement updated.');
    }
    public function destroy(Announcement $announcement) {
        if ($announcement->image) Storage::disk('public')->delete($announcement->image);
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success','Announcement deleted.');
    }
    public function publish(Announcement $announcement) {
        $announcement->update(['status' => 'published','published_at' => now()]);
        return back()->with('success','Announcement published.');
    }
    public function archive(Announcement $announcement) {
        $announcement->update(['status' => 'archived','archived_at' => now()]);
        return back()->with('success','Announcement archived.');
    }
}
