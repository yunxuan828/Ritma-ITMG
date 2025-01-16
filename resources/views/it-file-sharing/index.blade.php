@extends('layouts.default')
@section('title')
   IT File Sharing
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Files</h3>
                @can('create', App\Models\ITFileSharing::class)
                    <div class="box-tools pull-right">
                        <a href="{{ route('it-file-sharing.create') }}" class="btn btn-primary">Upload New File</a>
                    </div>
                @endcan
            </div>
            <div class="box-body">
                <!-- Search Form -->
                <form action="{{ route('it-file-sharing.index') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search files..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
                <table class="table">
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
                            <td>{{ $file->uploader ? $file->uploader->first_name . ' ' . $file->uploader->last_name : 'Unknown' }}</td>
                            <td>{{ $file->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $file->updated_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <a href="{{ route('it-file-sharing.show', $file) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('it-file-sharing.download', $file->id) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-download"></i> Download
                                </a>
                                <a href="{{ route('it-file-sharing.edit', $file) }}" class="btn btn-sm btn-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('it-file-sharing.destroy', $file) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection