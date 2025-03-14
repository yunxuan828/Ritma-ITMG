@extends('layouts/default')

{{-- Page title --}}
@section('title')
IT File Sharing
@parent
@stop

@section('header_right')
@can('create', \App\Models\ITFolder::class)
    <a href="{{ route('it-folders.create') }}" accesskey="n" class="btn btn-primary pull-right">
      Create New Folder
    </a>
@endcan
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-body">
        <div class="table-responsive">
          <table
              data-cookie-id-table="foldersTable"
              data-pagination="true"
              data-search="true"
              data-side-pagination="client"
              data-show-columns="true"
              data-show-fullscreen="true"
              data-show-export="false"
              data-show-footer="false"
              data-show-refresh="true"
              data-sort-order="asc"
              data-sort-name="name"
              id="foldersTable"
              class="table table-striped snipe-table"
              data-export-options='{
                "fileName": "export-folders-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions"]
              }'>
            <thead>
              <tr>
                <th data-sortable="true">Name</th>
                <th data-sortable="true">Description</th>
                <th data-sortable="true">Files Count</th>
                <th data-sortable="true">Created By</th>
                <th data-sortable="false" class="no-sort">Actions</th>
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
                      <a href="{{ route('it-folders.edit', $folder->id) }}" class="btn btn-sm btn-warning">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                      </a>
                    @endcan
                    @can('delete', $folder)
                      @if($folder->files_count == 0)
                        <form action="{{ route('it-folders.destroy', $folder->id) }}" 
                              method="POST" style="display: inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger" 
                                  onclick="return confirm('Are you sure you want to delete this folder?')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
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
