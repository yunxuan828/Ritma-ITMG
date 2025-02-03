<?php

namespace App\Http\Controllers;

use App\Models\ITFileSharing;
use App\Models\ITFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ITFileSharingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(ITFileSharing::class, 'itFileSharing');
    }

    public function index()
    {
        $folders = ITFolder::withCount('files')
                          ->with('creator')
                          ->get();
        return view('IT-Sharing.index', compact('folders'));
    }

    public function files($folderId)
    {
        $folder = ITFolder::findOrFail($folderId);
        $files = ITFileSharing::with(['uploader'])
                             ->where('folder_id', $folderId)
                             ->paginate(10);
        return view('IT-Sharing.files', compact('files', 'folder'));
    }

    public function create()
    {
        $folders = ITFolder::all();
        return view('IT-Sharing.create', compact('folders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'required|exists:i_t_folders,id',
            'file' => 'required|file|max:10240'
        ]);

        $file = $request->file('file');
        $path = $file->store('it-files');

        $validated['file_path'] = $path;
        $validated['original_filename'] = $file->getClientOriginalName();
        $validated['uploaded_by'] = Auth::id();

        ITFileSharing::create($validated);

        return redirect()->route('it-file-sharing.files', $validated['folder_id'])
                        ->with('success', 'File uploaded successfully.');
    }

    public function edit(ITFileSharing $itFileSharing)
    {
        $folders = ITFolder::all();
        return view('IT-Sharing.edit', compact('itFileSharing', 'folders'));
    }

    public function update(Request $request, ITFileSharing $itFileSharing)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'required|exists:i_t_folders,id'
        ]);

        $itFileSharing->update($validated);

        return redirect()->route('it-file-sharing.files', $itFileSharing->folder_id)
                        ->with('success', 'File updated successfully.');
    }

    public function destroy(ITFileSharing $itFileSharing)
    {
        $folderId = $itFileSharing->folder_id;
        Storage::delete($itFileSharing->file_path);
        $itFileSharing->delete();

        return redirect()->route('it-file-sharing.files', $folderId)
                        ->with('success', 'File deleted successfully.');
    }

    public function download(ITFileSharing $itFileSharing)
    {
        return Storage::download(
            $itFileSharing->file_path,
            $itFileSharing->original_filename
        );
    }
}