@extends('layouts.default')

{{-- Page title --}}
@section('title')
    Upload IT File
@parent
@stop

@section('header_right')
    <a href="{{ route('it-file-sharing.index') }}" class="btn btn-primary pull-right">
        Back to Folders
    </a>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Upload New File</h3>
            </div>
            <div class="box-body">
                <form class="form-horizontal" method="POST" action="{{ route('it-file-sharing.store') }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Folder -->
                    <div class="form-group {{ $errors->has('folder_id') ? ' has-error' : '' }}">
                        <label for="folder_id" class="col-md-3 control-label">Folder</label>
                        <div class="col-md-7">
                            <select class="form-control" id="folder_id" name="folder_id" required>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" 
                                            {{ old('folder_id', request('folder_id')) == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('folder_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <!-- Files -->
                    <div class="form-group {{ $errors->has('files') ? ' has-error' : '' }}">
                        <label for="files" class="col-md-3 control-label">Files</label>
                        <div class="col-md-7">
                            <input type="file" class="form-control" id="files" name="files[]" multiple required onchange="handleFileSelect(this)">
                            <small class="form-text text-muted">Maximum file size: 50MB per file. Multiple files allowed.</small>
                            {!! $errors->first('files', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <!-- File Descriptions (dynamically added) -->
                    <div id="file-descriptions"></div>
                    
                    <!-- Form Actions -->
                    <div class="form-group">
                        <div class="col-md-7 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@push('js')
<script>
function handleFileSelect(input) {
    const container = document.getElementById('file-descriptions');
    container.innerHTML = '';

    Array.from(input.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'form-group';
        
        const label = document.createElement('label');
        label.className = 'col-md-3 control-label';
        label.textContent = `Description for ${file.name}`;
        
        const inputDiv = document.createElement('div');
        inputDiv.className = 'col-md-7';
        
        const textarea = document.createElement('textarea');
        textarea.className = 'form-control';
        textarea.name = `descriptions[]`;
        textarea.rows = 2;
        textarea.placeholder = `Enter description for ${file.name}`;
        
        inputDiv.appendChild(textarea);
        
        div.appendChild(label);
        div.appendChild(inputDiv);
        
        container.appendChild(div);
    });
}
</script>
@endpush
