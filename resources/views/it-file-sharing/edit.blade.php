@extends('layouts.default')

@section('title')
    Edit IT File
@endsection

@section('content')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Edit File</h3>
    </div>
    <div class="box-body">
        <form action="{{ route('it-file-sharing.update', $itFileSharing) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ $itFileSharing->title }}" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ $itFileSharing->description }}</textarea>
            </div>
            <div class="form-group">
                <label>Current File</label>
                <p id="currentFile">{{ basename($itFileSharing->file_path) }}</p>
                <label>Upload New File (Optional)</label>
                <input type="file" name="file" id="fileInput" class="form-control">
                <p id="selectedFile" class="text-info mt-2"></p>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('it-file-sharing.index') }}" class="btn btn-default">Cancel</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('fileInput').addEventListener('change', function() {
    var fileName = this.files[0] ? this.files[0].name : '';
    document.getElementById('selectedFile').innerHTML = fileName ? 'Selected file: ' + fileName : '';
});
</script>
@endpush
@endsection