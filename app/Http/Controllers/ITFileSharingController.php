<?php

namespace App\Http\Controllers;

use App\Models\ITFileSharing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ITFileSharingController extends Controller
{
    public function index(Request $request)
    {
        $query = ITFileSharing::with('uploader');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('uploader', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
        }

        $files = $query->get();

        return view('it-file-sharing.index', compact('files'));
    }

    public function create()
    {
        $this->authorize('create', ITFileSharing::class);
        return view('it-file-sharing.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', ITFileSharing::class);
    
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240'
        ]);
    
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('', $originalName, 'public_files'); // Use 'public_files' disk

        ITFileSharing::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $path,
            'uploaded_by' => auth()->id()
        ]);
    
        return redirect()->route('it-file-sharing.index')
            ->with('success', 'File uploaded successfully');
    }

    public function edit(ITFileSharing $itFileSharing)
    {
        $this->authorize('update', ITFileSharing::class);
        return view('it-file-sharing.edit', compact('itFileSharing'));
    }

    public function update(Request $request, ITFileSharing $itFileSharing)
    {
        $this->authorize('update', ITFileSharing::class);
    
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'file' => 'nullable|file|max:10240'
        ]);

        $itFileSharing->title = $validated['title'];
        $itFileSharing->description = $validated['description'];

        if ($request->hasFile('file')) {

        // Delete the old file if it exists
        if (Storage::disk('public_files')->exists($itFileSharing->file_path)) {
            Storage::disk('public_files')->delete($itFileSharing->file_path);
        }
        
        // Store new file
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->storeAs('', $originalName, 'public_files'); // Use 'public_files' disk
        $itFileSharing->file_path = $path;
        }   

        $itFileSharing->save();

        return redirect()->route('it-file-sharing.index')
        ->with('success', 'File updated successfully');
    }

    public function destroy(ITFileSharing $itFileSharing)
    {
        $this->authorize('delete', $itFileSharing);
        
        Storage::disk('public_files')->delete($itFileSharing->file_path);
        $itFileSharing->delete();

    return redirect()->route('it-file-sharing.index')
        ->with('success', 'File deleted successfully');
    }

    public function show(ITFileSharing $itFileSharing)
    {
        return view('it-file-sharing.show', compact('itFileSharing'));
    }


    public function download($fileId)
    {
        $file = ITFileSharing::findOrFail($fileId);

        $filePath = $file->file_path;
        $fileName = basename($filePath);

        if (Storage::disk('public_files')->exists($filePath)) {
            return response()->streamDownload(function () use ($filePath) {
                echo Storage::disk('public_files')->get($filePath);
            }, $fileName, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }

        return redirect()->back()->with('error', 'File not found.');
    }
}