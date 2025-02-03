@extends('layouts.default')

@section('title')
    Files - {{ $folder->name }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Files in {{ $folder->name }}</h3>
                <div class="box-tools pull-right">
                    @can('create', App\Models\ITFileSharing::class)
                        <a href="{{ route('it-file-sharing.create', ['folder_id' => $folder->id]) }}" class="btn btn-success">
                            Upload New File
                        </a>
                    @endcan
                    <a href="{{ route('it-file-sharing.index') }}" class="btn btn-default">
                        Back to Folders
                    </a>
                </div>
            </div>

            <div class="box-body">
                <!-- Search Form -->
                <form action="{{ route('it-file-sharing.files', $folder->id) }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search files..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Files Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Uploaded By</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr>
                                <td>{{ $file->title }}</td>
                                <td>{{ $file->description }}</td>
                                <td>{{ optional($file->uploader)->name ?? 'Unknown' }}</td>
                                <td>{{ $file->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $file->updated_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <a href="{{ route('it-file-sharing.download', $file) }}" 
                                       class="btn btn-sm btn-success">
                                        Download
                                    </a>
                                    @can('update', $file)
                                        <a href="{{ route('it-file-sharing.edit', $file) }}" 
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                    @endcan
                                    @can('delete', $file)
                                        <form action="{{ route('it-file-sharing.destroy', $file) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this file?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $files->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
