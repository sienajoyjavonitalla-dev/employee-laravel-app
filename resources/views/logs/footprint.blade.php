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
                        <th>Entity</th>
                        <th>E. ID</th>
                        <th>Field</th>
                        <th>Prev Value</th>
                        <th>New Value</th>
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
            {data: 'entity', name: 'entity'},
            {data: 'entity_id', name: 'entity_id'},
            {data: 'field', name: 'field'},
            {data: 'prev_value', name: 'prev_value'},
            {data: 'new_value', name: 'new_value'},
            {data: 'created_at', name: 'created_at'},
        ],
        "columnDefs": [
            { "width": "10%", "targets": [1,2,3,4] },
            { "width": "20%", "targets": [5,6,7] }
        ],
        order: [[0, 'asc']]
    });
    
});

</script>
@stop