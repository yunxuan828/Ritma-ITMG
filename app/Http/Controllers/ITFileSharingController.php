<?php

namespace App\Http\Controllers;

use App\Models\ITFileSharing;
use App\Models\ITFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;

class ITFileSharingController extends Controller
{
    /**
     * Constructor
     * 
     * Sets up middleware and authorization
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if user is an administrator
            if (!auth()->user()->isSuperUser()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
        $this->authorizeResource(ITFileSharing::class, 'itFileSharing');
    }

    /**
     * Display a listing of folders
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $folders = ITFolder::withCount('files')
                          ->with('creator')
                          ->get();
        return view('IT-Sharing.index', compact('folders'));
    }

    /**
     * Display files in a folder
     * 
     * @param int $folderId
     * @return \Illuminate\Contracts\View\View
     */
    public function files($folderId)
    {
        $folder = ITFolder::findOrFail($folderId);
        $files = ITFileSharing::with(['uploader'])
                             ->where('folder_id', $folderId)
                             ->get();
        return view('IT-Sharing.files', compact('files', 'folder'));
    }

    /**
     * Show the form for creating a new file
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $folders = ITFolder::all();
        $item = new ITFileSharing();
        return view('IT-Sharing.create', compact('folders', 'item'));
    }

    /**
     * Store a newly created file in storage
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'required|exists:i_t_folders,id',
            'files' => 'required|array',
            'files.*' => 'file|max:51200', // 50MB in KB
            'titles' => 'array',
            'titles.*' => 'nullable|string|max:255',
            'descriptions' => 'array',
            'descriptions.*' => 'nullable|string'
        ]);

        $uploadedFiles = [];
        foreach ($request->file('files') as $index => $file) {
            $originalName = $file->getClientOriginalName();
            $fileTitle = $request->titles[$index] ?? pathinfo($originalName, PATHINFO_FILENAME);
            $path = $file->storeAs('it-files', $originalName, 'public_files');
            
            $fileData = [
                'title' => $fileTitle,
                'description' => $request->descriptions[$index] ?? null,
                'folder_id' => $validated['folder_id'],
                'file_path' => $path,
                'original_filename' => $originalName,
                'uploaded_by' => Auth::id()
            ];
            
            $uploadedFiles[] = ITFileSharing::create($fileData);
        }

        return redirect()->route('it-file-sharing.files', $validated['folder_id'])
                        ->with('success', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    /**
     * Show the form for editing the specified file
     * 
     * @param ITFileSharing $itFileSharing
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(ITFileSharing $itFileSharing)
    {
        $folders = ITFolder::all();
        $item = $itFileSharing;
        return view('IT-Sharing.edit', compact('folders', 'item'));
    }

    /**
     * Update the specified file in storage
     * 
     * @param Request $request
     * @param ITFileSharing $itFileSharing
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ITFileSharing $itFileSharing)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'folder_id' => 'required|exists:i_t_folders,id',
            'file' => 'nullable|file|max:10240'
        ]);

        $itFileSharing->title = $validated['title'];
        $itFileSharing->description = $validated['description'];
        $itFileSharing->folder_id = $validated['folder_id'];

        // Handle file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($itFileSharing->file_path && Storage::disk('public_files')->exists($itFileSharing->file_path)) {
                Storage::disk('public_files')->delete($itFileSharing->file_path);
            }

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('it-files', $originalName, 'public_files');
            
            $itFileSharing->file_path = $path;
            $itFileSharing->original_filename = $originalName;
        }

        $itFileSharing->save();

        return redirect()->route('it-file-sharing.files', $itFileSharing->folder_id)
                        ->with('success', 'File updated successfully.');
    }

    /**
     * Remove the specified file from storage
     * 
     * @param ITFileSharing $itFileSharing
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ITFileSharing $itFileSharing)
    {
        $folderId = $itFileSharing->folder_id;

        // Delete the file from storage
        if ($itFileSharing->file_path && Storage::disk('public_files')->exists($itFileSharing->file_path)) {
            Storage::disk('public_files')->delete($itFileSharing->file_path);
        }

        $itFileSharing->delete();

        return redirect()->route('it-file-sharing.files', $folderId)
                        ->with('success', 'File deleted successfully.');
    }

    /**
     * Download the specified file
     * 
     * @param ITFileSharing $itFileSharing
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(ITFileSharing $itFileSharing)
    {
        if (!Storage::disk('public_files')->exists($itFileSharing->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $path = Storage::disk('public_files')->path($itFileSharing->file_path);
        return response()->download($path, $itFileSharing->original_filename);
    }
}
