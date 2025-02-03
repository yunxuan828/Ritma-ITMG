<?php

namespace App\Http\Controllers;

use App\Models\ITFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ITFolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(ITFolder::class, 'folder');
    }

    public function index()
    {
        $folders = ITFolder::withCount('files')->get();
        return view('IT-Sharing.index', compact('folders'));
    }

    public function create()
    {
        return view('IT-Sharing.create-folder');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:i_t_folders,name',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        ITFolder::create($validated);

        return redirect()->route('it-file-sharing.index')
            ->with('success', 'Folder created successfully.');
    }

    public function edit(ITFolder $folder)
    {
        return view('IT-Sharing.edit-folder', ['folder' => $folder]);
    }

    public function update(Request $request, ITFolder $folder)
    {
        if ($folder->name === 'Uncategorized') {
            return back()->with('error', 'The Uncategorized folder cannot be modified.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:i_t_folders,name,' . $folder->id,
            'description' => 'nullable|string',
        ]);

        $folder->update($validated);

        return redirect()->route('it-file-sharing.index')
            ->with('success', 'Folder updated successfully.');
    }

    public function destroy(ITFolder $folder)
    {
        if ($folder->name === 'Uncategorized') {
            return back()->with('error', 'The Uncategorized folder cannot be deleted.');
        }

        if ($folder->files()->exists()) {
            return back()->with('error', 'Cannot delete folder that contains files.');
        }

        $folder->delete();

        return redirect()->route('it-file-sharing.index')
            ->with('success', 'Folder deleted successfully.');
    }
}
