@extends('layouts.default')

{{-- Page title --}}
@section('title')
    Edit File - {{ $item->title }}
@parent
@stop

@section('header_right')
    <a href="{{ route('it-file-sharing.files', $item->folder_id) }}" class="btn btn-primary pull-right">
        Back to Files
    </a>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Edit File</h3>
            </div>
            <div class="box-body">
                <form class="form-horizontal" method="POST" action="{{ route('it-file-sharing.update', $item->id) }}" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Title -->
                    <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                        <label for="title" class="col-md-3 control-label">Title</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="title" name="title" 
                                value="{{ old('title', $item->title) }}" required>
                            {!! $errors->first('title', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description" class="col-md-3 control-label">Description</label>
                        <div class="col-md-7">
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                            {!! $errors->first('description', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <!-- Folder -->
                    <div class="form-group {{ $errors->has('folder_id') ? ' has-error' : '' }}">
                        <label for="folder_id" class="col-md-3 control-label">Folder</label>
                        <div class="col-md-7">
                            <select class="form-control" id="folder_id" name="folder_id" required>
                                @foreach($folders as $folder)
                                    <option value="{{ $folder->id }}" 
                                            {{ old('folder_id', $item->folder_id) == $folder->id ? 'selected' : '' }}>
                                        {{ $folder->name }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('folder_id', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>

                    <!-- File -->
                    <div class="form-group {{ $errors->has('file') ? ' has-error' : '' }}">
                        <label for="file" class="col-md-3 control-label">File</label>
                        <div class="col-md-7">
                            <input type="file" class="form-control" id="file" name="file">
                            <small class="form-text text-muted">Current file: {{ $item->original_filename ?? 'None' }}<br>
                            Leave empty to keep current file. Maximum file size: 50MB.</small>
                            {!! $errors->first('file', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-group">
                        <div class="col-md-7 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop
