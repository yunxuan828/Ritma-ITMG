@extends('layouts.default')

@section('title')
    IT File Sharing
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Folders</h3>
                <div class="box-tools pull-right">
                    @can('create', App\Models\ITFolder::class)
                        <a href="{{ route('it-folders.create') }}" class="btn btn-primary">
                            Create New Folder
                        </a>
                    @endcan
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Files Count</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($folders as $folder)
                            <tr>
                                <td>
                                    <a href="{{ route('it-file-sharing.files', $folder->id) }}">
                                        {{ $folder->name }}
                                    </a>
                                </td>
                                <td>{{ $folder->description }}</td>
                                <td>{{ $folder->files_count }}</td>
                                <td>{{ optional($folder->creator)->first_name . ' ' . optional($folder->creator)->last_name ?? 'Unknown' }}</td>
                                <td>
                                    @if($folder->name !== 'Uncategorized')
                                        @can('update', $folder)
                                            <a href="{{ route('it-folders.edit', $folder) }}" class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $folder)
                                            @if($folder->files_count == 0)
                                                <form action="{{ route('it-folders.destroy', $folder) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this folder?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
