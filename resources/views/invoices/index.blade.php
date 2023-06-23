@extends('layouts.app')

@section('content')
<div class="card p-3 shadow" >
        <h2>Invoices</h2>
        <div class="tab-pane fade active show" id="nav-invoice" role="tabpanel" aria-labelledby="nav-invoice-tab">
            <nav>
                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-list-tab" data-bs-toggle="tab" data-bs-target="#nav-list" type="button" role="tab" aria-controls="nav-list" aria-selected="true">List</button>
                    <!-- <button class="nav-link" id="nav-generate-tab" data-bs-toggle="tab" data-bs-target="#nav-generate" type="button" role="tab" aria-controls="nav-generate" aria-selected="false">Generate</button> -->
                </div>
            </nav>
            <div class="tab-content p-3 border bg-light" id="nav-tabContent">
                <div class="tab-pane fade active show" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                    <table class="table table-bordered table-hover invoice-table">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>URL</th>
                                <th>Is Emailed?</th>
                                <th>Job</th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-generate" role="tabpanel" aria-labelledby="nav-generate-tab">
                    <div class="row">
                        <div class="col-lg-12 align-items-center p-4">
                            <div class="container">
                            <div class="row">
                                <div class="col-lg-6">
                                    <strong>Date Filter </strong>
                                    <input type="text" name="daterange" value="" />
                                </div>
                                <div class="col-lg-6">
                                    <strong>Client </strong>
                                    <select  name="client" id="client">
                                        <option value="">-- Select --</option>
                                        @foreach ($clients as $key => $value)
                                            <option value="{{ $key }}"> 
                                                {{ $value }} 
                                            </option>
                                        @endforeach    
                                    </select>
                                </div>
                                
                            </div>
                            <button class="w-auto mb-4 mt-4 btn btn-success filter"><i class="fas fa-search"></i> Filter</button>
                            <a href="{{ route('invoices.generate.pdf',['download'=>'pdf']) }}" class="btn btn-primary"><i class="fas fa-print"></i> Generate Invoice</a>

                            <table class="table table-bordered table-hover data-table" width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Job</th>
                                        <th>PO#</th>
                                        <th>Employee</th>
                                        <th>Client</th>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Hours Worked</th>
                                        <th>Lunch</th>
                                        <th>Pay</th>
                                        <th>OT Pay</th>
                                        <th>Total Pay</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
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

    var invoice_table = $('.invoice-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('invoices.index', ['name'=>'list']) }}",
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
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        startDate: moment().subtract(1, 'M'),
        endDate: moment()
    });
    load_data($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'), 
    $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'), 
    null,null,null);

    function load_data(from_date, to_date, client, po_number, assigned) {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 8,
            ajax: {

                url: "{{ route('invoices.index', ['name'=>'generate']) }}",
                data:{
                    from_date: from_date, 
                    to_date: to_date,
                    client: client,
                    po_number:po_number,
                    assigned: assigned
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'po_number', name: 'po_number'},
                {data: 'employee', name: 'employee'},
                {data: 'company_name', name: 'company_name'},
                {data: 'title', name: 'title'},
                {data: 'date', name: 'date'},
                {data: 'hrs_worked', name: 'hrs_worked'},
                {data: 'with_lunch', name: 'with_lunch'},
                {data: 'pay', name: 'pay'},
                {data: 'ot_pay', name: 'ot_pay'},
                {data: 'total', name: 'total'},
            ],
            "columnDefs": [
                { "width": "2%", "targets": [7] }

            ],
        });
    }
    

    $(".filter").click(function(){
        var from_date = ($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'));
        var to_date = ($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'));
        var client = $('#client').val();
        var po_number = $('#po_number').val();
        var assigned = $('#assigned').val();

        if(from_date != '' &&  to_date != '')
        {
            $('.data-table').DataTable().destroy();
            load_data(from_date, to_date, client, po_number, assigned);
        }
        else
        {
            toastr.error('Both Date is required!');
        }
    });

    $('.generate').click(function (e) {
        var from_date = ($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'));
        var to_date = ($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'));
        var client = $('#client').val();
        var po_number = $('#po_number').val();
        var assigned = $('#assigned').val();

        var dataToSend = {
            'from_date': from_date,
            'to_date': to_date,
            'client': client,
            'po_number': po_number,
            'assigned': assigned
        };

        $.ajax({
            url: "generatePDF",
            type: "GET",
            dataType: 'json',
            data: dataToSend,
            success: function (data) {
                if(data.success) {
                    toastr.success(data.success, 'SUCCESS');
                    window.location.reload();        
                } else {
                    toastr.error(data.error, 'ERROR');
                }
            },
            error: function (data) {
                toastr.error('Error Saving!');

            }
        });
    })
  });

</script>
@stop