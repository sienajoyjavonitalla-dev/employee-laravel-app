@extends('layouts.app')

@section('content')
	<div class="card p-3 shadow" >
        <h2>Invoices</h2>
        <div class="tab-pane fade active show" id="nav-invoice" role="tabpanel" aria-labelledby="nav-invoice-tab">
            <nav>
                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-list-tab" data-bs-toggle="tab" data-bs-target="#nav-list" type="button" role="tab" aria-controls="nav-list" aria-selected="true">List</button>
                    <button class="nav-link" id="nav-generate-tab" data-bs-toggle="tab" data-bs-target="#nav-generate" type="button" role="tab" aria-controls="nav-generate" aria-selected="false">Generate</button>
                </div>
            </nav>
            <div class="tab-content p-3 border bg-light" id="nav-tabContent">
                <div class="tab-pane fade active show" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                <table class="table table-bordered table-hover data-table">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>URL</th>
                            <th>Is Emailed?</th>
                            <th>Jobs</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
                <div class="tab-pane fade" id="nav-generate" role="tabpanel" aria-labelledby="nav-generate-tab">
                <table class="table table-bordered table-hover jobs-table" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Assigned Persons</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </div>
                
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
        ajax: "{{ route('client.show-invoices', ['client'=>$client_id, 'name'=>'list']) }}",
        columns: [
            {data: 'invoice_id', name: 'invoice_id'},
            {data: 'status', name: 'status'},
            {data: 'online_invoice_url', name: 'online_invoice_url'},
            {data: 'emailed', name: 'emailed'},
            {data: 'id', name: 'id'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
        ],
        order: [[0, 'asc']]
    });
    var table = $('.jobs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('client.show-invoices', ['client'=>$client_id, 'name'=>'generate']) }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'title', name: 'title'},
            {data: 'employee_id', name: 'employee_id'},
            {data: 'status', name: 'status'},
            {data: 'start_date_time', name: 'start_date_time'},
            {data: 'end_date_time', name: 'end_date_time'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
        ],
        order: [[0, 'asc']]
    });
  });

</script>
@stop