@extends('layouts/default')

{{-- Page title --}}
@section('title')
Files - {{ $folder->name }}
@parent
@stop

@section('header_right')
@can('create', \App\Models\ITFileSharing::class)
    <a href="{{ route('it-file-sharing.create', ['folder_id' => $folder->id]) }}" accesskey="n" class="btn btn-success pull-right">
      Upload New File
    </a>
@endcan
<a href="{{ route('it-file-sharing.index') }}" class="btn btn-default pull-right" style="margin-right: 5px;">
    Back to Folders
</a>
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-body">
        <!-- Files Table -->
        <div class="table-responsive">
          <table
              data-cookie-id-table="filesTable"
              data-pagination="true"
              data-search="true"
              data-side-pagination="client"
              data-show-columns="true"
              data-show-fullscreen="true"
              data-show-export="true"
              data-show-footer="false"
              data-show-refresh="true"
              data-sort-order="asc"
              data-sort-name="title"
              id="filesTable"
              class="table table-striped snipe-table"
              data-export-options='{
                "fileName": "export-files-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions"]
              }'>
            <thead>
              <tr>
                <th data-sortable="true">Title</th>
                <th data-sortable="true">Description</th>
                <th data-sortable="true">Uploaded By</th>
                <th data-sortable="true">Created At</th>
                <th data-sortable="true">Updated At</th>
                <th data-sortable="false" class="no-sort">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($files as $file)
              <tr>
                <td>{{ $file->title }}</td>
                <td>{{ $file->description }}</td>
                <td>{{ optional($file->uploader)->first_name . ' ' . optional($file->uploader)->last_name ?? 'Unknown' }}</td>
                <td>{{ $file->created_at->format('Y-m-d H:i:s') }}</td>
                <td>{{ $file->updated_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('it-file-sharing.download', $file) }}" 
                       class="btn btn-sm btn-default">
                      <i class="fa fa-download"></i>
                    </a>
                    @can('update', $file)
                      <a href="{{ route('it-file-sharing.edit', $file) }}" 
                         class="btn btn-sm btn-warning">
                        <i class="fa fa-pencil"></i>
                      </a>
                    @endcan
                    @can('delete', $file)
                      <form action="{{ route('it-file-sharing.destroy', $file) }}" 
                            method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this file?')">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- /.box-body -->

      <div class="box-footer clearfix">
      </div>
    </div><!-- /.box -->
  </div>
</div>
@stop

@section('moar_scripts')
@include ('partials.bootstrap-table')
@stop
