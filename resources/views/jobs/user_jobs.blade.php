@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">

        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>Job ID</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Address</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Assigned</th>
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
        pageLength: 8,
        responsive: true,
        ajax: "{{ route('user.jobs') }}",

        columns: [
            {data: 'id', name: 'id'},
            {data: 'client', name: 'client'},
            {data: 'status', name: 'status'},
            {data: 'address', name: 'address'},
            {data: 'start_date_time', name: 'start_date_time'},
            {data: 'end_date_time', name: 'end_date_time'},
            {data: 'assigned', name: 'assigned'}
        ],
        order: [[4, 'asc']]
    });
  });

</script>
@stop