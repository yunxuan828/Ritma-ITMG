@extends('layouts.default')

@section('title')
    Edit File - {{ $itFileSharing->title }}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Edit File</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('it-file-sharing.update', $itFileSharing) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $itFileSharing->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description">{{ old('description', $itFileSharing->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="folder_id">Folder</label>
                        <select class="form-control @error('folder_id') is-invalid @enderror" 
                                id="folder_id" name="folder_id" required>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}" 
                                        {{ old('folder_id', $itFileSharing->folder_id) == $folder->id ? 'selected' : '' }}>
                                    {{ $folder->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('folder_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="file">File (Optional - Leave empty to keep current file)</label>
                        <input type="file" class="form-control-file @error('file') is-invalid @enderror" 
                               id="file" name="file">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Current file: {{ $itFileSharing->original_filename }}</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update File</button>
                        <a href="{{ route('it-file-sharing.files', $itFileSharing->folder_id) }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
