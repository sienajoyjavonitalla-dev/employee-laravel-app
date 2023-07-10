@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">
            <table class="table table-bordered table-hover data-table">
                <thead class="thead-light">
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script type="text/javascript">

$(function () {

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.footprint') }}",
        columns: [
            {data: 'user_id', name: 'user_id'},
            {data: 'action_type', name: 'action_type'},
            {data: 'description', name: 'description'},
            {data: 'created_at', name: 'created_at'},
        ],
        order: [[0, 'asc']]
    });

});

</script>
@stop