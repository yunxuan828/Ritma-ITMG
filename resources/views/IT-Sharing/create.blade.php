@extends('layouts.default')

@section('title')
    Upload IT File
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Upload New File</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('it-file-sharing.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="folder_id">Folder</label>
                        <select class="form-control @error('folder_id') is-invalid @enderror" 
                                id="folder_id" name="folder_id" required>
                            @foreach($folders as $folder)
                                <option value="{{ $folder->id }}" 
                                        {{ old('folder_id', request('folder_id')) == $folder->id ? 'selected' : '' }}>
                                    {{ $folder->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('folder_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="files">Files</label>
                        <input type="file" class="form-control @error('files') is-invalid @enderror" 
                               id="files" name="files[]" multiple required onchange="handleFileSelect(this)">
                        <small class="form-text text-muted">Maximum file size: 10MB per file. Multiple files allowed.</small>
                        @error('files')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="file-descriptions"></div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                        <a href="{{ route('it-file-sharing.index') }}" class="btn btn-default">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function handleFileSelect(input) {
    const container = document.getElementById('file-descriptions');
    container.innerHTML = '';

    Array.from(input.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'form-group';
        div.innerHTML = `
            <label>Description for ${file.name}</label>
            <textarea class="form-control" name="descriptions[]" rows="2"
                      placeholder="Enter description for ${file.name}"></textarea>
        `;
        container.appendChild(div);
    });
}
</script>
@endpush
@endsection
