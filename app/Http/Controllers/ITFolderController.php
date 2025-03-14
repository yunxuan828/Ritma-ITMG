<?php

namespace App\Http\Controllers;

use App\Models\ITFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class ITFolderController extends Controller
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
        $this->authorizeResource(ITFolder::class, 'folder');
    }

    /**
     * Display a listing of folders
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $folders = ITFolder::withCount('files')->get();
        return view('IT-Sharing.index', compact('folders'));
    }

    /**
     * Show the form for creating a new folder
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $item = new ITFolder();
        return view('IT-Sharing.create-folder', compact('item'));
    }

    /**
     * Store a newly created folder in storage
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:i_t_folders,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string',
        ]);

        $folder = new ITFolder();
        $folder->name = $validated['name'];
        $folder->description = $validated['description'];
        $folder->created_by = Auth::id();
        $folder->save();

        return redirect()->route('it-file-sharing.index')
            ->with('success', 'Folder created successfully.');
    }

    /**
     * Show the form for editing the specified folder
     * 
     * @param int $folderId
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($folderId)
    {
        $folder = ITFolder::findOrFail($folderId);
        return view('IT-Sharing.edit-folder', ['item' => $folder]);
    }

    /**
     * Update the specified folder in storage
     * 
     * @param Request $request
     * @param int $folderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $folderId)
    {
        $folder = ITFolder::findOrFail($folderId);
        
        if ($folder->name === 'Uncategorized') {
            return back()->with('error', 'The Uncategorized folder cannot be modified.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:i_t_folders,name,' . $folder->id . ',id,deleted_at,NULL',
            'description' => 'nullable|string',
        ]);

        $folder->name = $validated['name'];
        $folder->description = $validated['description'];
        $folder->save();

        return redirect()->route('it-file-sharing.index')
            ->with('success', 'Folder updated successfully.');
    }

    /**
     * Remove the specified folder from storage
     * 
     * @param int $folderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($folderId)
    {
        $folder = ITFolder::findOrFail($folderId);
        
        if ($folder->name === 'Uncategorized') {
            return back()->with('error', 'The Uncategorized folder cannot be deleted.');
        }

        if (!$folder->canDelete()) {
            return back()->with('error', 'Cannot delete folder that contains files. Please move or delete the files first.');
        }

        $folder->delete();

        return redirect()->route('it-file-sharing.index')
            ->with('success', 'Folder deleted successfully.');
    }

    /**
     * Export IT folders to CSV
     */
    public function export()
    {
        // Check if user is authorized to view ITFolder
        $this->authorize('view', ITFolder::class);
        
        // Additional check for admin access
        if (!auth()->user()->isSuperUser()) {
            abort(403, 'Unauthorized action.');
        }
        
        $folders = ITFolder::withCount('files')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="it-folders-' . date('Y-m-d') . '.csv"',
        ];
        
        $columns = ['ID', 'Name', 'Description', 'Files Count', 'Created By', 'Created At', 'Updated At'];
        
        $callback = function() use ($folders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($folders as $folder) {
                $row = [
                    $folder->id,
                    $folder->name,
                    $folder->description,
                    $folder->files_count,
                    optional($folder->creator)->first_name . ' ' . optional($folder->creator)->last_name,
                    $folder->created_at,
                    $folder->updated_at,
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
