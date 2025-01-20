@extends('layouts.default')

@section('title')
    View File Details
@endsection

@section('content')
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">File Details</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('it-file-sharing.index') }}" class="btn btn-default">Back to List</a>
            <a href="{{ Storage::disk('public_files')->url($itFileSharing->file_path) }}" class="btn btn-primary">
                <i class="fa fa-download"></i> Download
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $itFileSharing->title }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $itFileSharing->description }}</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Uploaded By:</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                {{ $itFileSharing->uploader ? $itFileSharing->uploader->first_name . ' ' . $itFileSharing->uploader->last_name : 'Unknown' }}
                            </p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="file-preview-container">
                    @php
                        $extension = strtolower(pathinfo($itFileSharing->file_path, PATHINFO_EXTENSION));
                    @endphp

                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($itFileSharing->file_path) }}" class="img-responsive" alt="Image Preview">
                    @elseif($extension === 'pdf')
                        <div class="pdf-container" style="height: 800px;">
                            <iframe src="{{ Storage::disk('public_files')->url($itFileSharing->file_path) }}" 
                                    width="100%" 
                                    height="100%" 
                                    frameborder="0">
                            </iframe>
                        </div>  
                    @elseif(in_array($extension, ['txt', 'csv', 'md']))
                        <pre class="text-preview">{{ Storage::get(str_replace('public_files', '', $itFileSharing->file_path)) }}</pre>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            Preview not available for this file type. Please download to view.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.file-preview-container {
    margin-top: 20px;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 4px;
}
.text-preview {
    max-height: 500px;
    overflow-y: auto;
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
}
.pdf-container {
    width: 100%;
    border: 1px solid #ddd;
}
</style>
@endpush
@endsection