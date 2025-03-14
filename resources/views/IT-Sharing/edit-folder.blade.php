@extends('layouts.default')

{{-- Page title --}}
@section('title')
    Edit Folder - {{ $item->name }}
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
                <h3 class="box-title">Edit Folder</h3>
            </div>
            <div class="box-body">
                <form class="form-horizontal" method="POST" action="{{ route('it-folders.update', $item->id) }}" autocomplete="off">
                    @csrf
                    @method('PUT')
                    
                    <!-- Folder Name -->
                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-3 control-label">Folder Name</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" id="name" name="name" 
                                value="{{ old('name', $item->name) }}" required>
                            {!! $errors->first('name', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
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
