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
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('it-files', $originalName, 'public_files');
        
        $validated['file_path'] = $path;
        $validated['original_filename'] = $originalName;
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'required|exists:i_t_folders,id',
            'file' => 'nullable|file|max:10240'
        ]);

        // Handle file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public_files')->delete($itFileSharing->file_path);
            
            // Store new file
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('it-files', $originalName, 'public_files');
            
            $validated['file_path'] = $path;
            $validated['original_filename'] = $originalName;
        }

        $itFileSharing->update($validated);

        return redirect()->route('it-file-sharing.files', $itFileSharing->folder_id)
                        ->with('success', 'File updated successfully.');
    }

    public function destroy(ITFileSharing $itFileSharing)
    {
        $folderId = $itFileSharing->folder_id;
        Storage::disk('public_files')->delete($itFileSharing->file_path);
        $itFileSharing->delete();

        return redirect()->route('it-file-sharing.files', $folderId)
                        ->with('success', 'File deleted successfully.');
    }

    public function download(ITFileSharing $itFileSharing)
    {
        // Check if file exists in storage
        if (!Storage::disk('public_files')->exists($itFileSharing->file_path)) {
            return back()->with('error', 'File not found.');
        }

        // Get file mime type
        $mimeType = Storage::disk('public_files')->mimeType($itFileSharing->file_path);

        // Return file download response
        return Storage::disk('public_files')->download(
            $itFileSharing->file_path,
            $itFileSharing->original_filename,
            ['Content-Type' => $mimeType]
        );
    }
}
