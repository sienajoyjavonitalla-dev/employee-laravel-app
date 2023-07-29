@extends('layouts.app')

@section('content')
<div class="card p-3 shadow" >
        <h4>Clients Invoice Management</h4>
        <div class="tab-pane fade active show" id="nav-invoice" role="tabpanel" aria-labelledby="nav-invoice-tab">
            <nav>
                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-list-tab" data-bs-toggle="tab" data-bs-target="#nav-list" type="button" role="tab" aria-controls="nav-list" aria-selected="true">List</button>
                    <button class="nav-link" id="nav-generate-tab" data-bs-toggle="tab" data-bs-target="#nav-generate" type="button" role="tab" aria-controls="nav-generate" aria-selected="false">Generate</button>
                </div>
            </nav>
            <div class="tab-content p-3 border bg-light" id="nav-tabContent">
                <div class="tab-pane fade active show" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                <h5 class="mb-5">Invoice List</h5>    
                <table class="table table-bordered table-hover invoice-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Job</th>
                                <th>ID</th>
                                <th>Status</th>
                                <th>URL</th>
                                <th>Created</th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="nav-generate" role="tabpanel" aria-labelledby="nav-generate-tab">
                <h5 class="mb-4">Generate Invoice</h5>    
                <div class="row">
                    <table class="table table-bordered table-hover data-table" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>Job ID</th>
                                <th>Invoice</th>
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
	</div>
    <div class="modal fade" id="generateModal" aria-hidden="true" width="100%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Generate Invoice</h4>
                </div>

                <div class="modal-body">
                    <div class="mb-5">
                        <span class="font-weight-bold">Job ID:</span> <span id="generate_job_id"></span></br>
                        <span class="font-weight-bold">Client:</span> <span id="company_name" class=></span>
                    </div>
                    <!-- <a href="{{ route('invoices.generate.pdf',['download'=>'pdf']) }}" class="btn btn-primary mb-3"><i class="fas fa-print"></i> Generate Invoice</a> -->
                    <a href="javascript:void(0)" class="btn btn-primary mb-3 postInvoiceBtn" id="generate_btn"><i class="fas fa-print"></i> Generate Invoice</a>

                    <table class="table table-bordered table-hover generate-table" >
                        <thead class="thead-light">
                            <tr>
                                <th>Assigned</th>
                                <th>Job Title</th>
                                <th>Date</th>
                                <th>Hrs Worked</th>
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
        ajax: "{{ route('clients_invoice', ['name'=>'list']) }}",
        columns: [
            {data: 'job_id', name: 'job_id'},
            {data: 'invoice_id', name: 'invoice_id'},
            {data: 'status', name: 'status'},
            {data: 'online_invoice_url', name: 'online_invoice_url'},
            {data: 'created', name: 'created'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
            { "width": "25%", "targets": [1] },
        ],
        order: [[4, 'desc']]

    });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 8,
        responsive: true,
        ajax: "{{ route('clients_invoice', ['name'=>'generate']) }}",

        columns: [
            {data: 'id', name: 'id'},
            {data: 'invoice', name: 'invoice'},
            {data: 'company_name', name: 'company_name'},
            {data: 'status', name: 'status'},
            {data: 'address', name: 'address'},
            {data: 'start_date_time', name: 'start_date_time'},
            {data: 'end_date_time', name: 'end_date_time'},
            {data: 'assigned', name: 'assigned'},
        ],
        "columnDefs": [
            { "width": "20%", "targets": [7] },
            { "width": "15%", "targets": [4] },
        ],
        order: [[5, 'desc']]
    });
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        startDate: moment().subtract(1, 'M'),
        endDate: moment()
    });

    $(".filter").click(function(){
        var from_date = ($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'));
        var to_date = ($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'));
        var client = $('#client').val();
        var job = $('#job').val();
        var assigned = $('#assigned').val();

        if(from_date != '' &&  to_date != '')
        {
            $('.data-table').DataTable().destroy();
            load_data(from_date, to_date, client, job, assigned);
        }
        else
        {
            toastr.error('Both Date is required!');
        }
    });

    $('body').on('click', '.generateBtn', function () {
        $('#generateModal').modal('show');
        var job_id = $(this).data('id');
        var company_name = $(this).data('company');
        var company_id = $(this).data('compid');

        $('#generate_job_id').text(job_id);
        $('#company_name').text(company_name);
        $('#generate_btn').attr("data-job", job_id);
        $('#generate_btn').attr("data-client", company_id);

        var generate_table = $('.generate-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 4,
            ajax: {

                url: "{{ route('jobs.index', ['name'=>'generate']) }}",
                data:{
                    job_id: job_id, 
                    company_id: company_id
                }
            },
            columns: [
                {data: 'employee', name: 'employee'},
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
        generate_table.destroy();

    });

    $('.postInvoiceBtn').click(function (e) {
        
        var job_id = $(this).attr('data-job');
        var client_id = $(this).attr('data-client');
        var dataToSend = {
            'job_id': job_id,
            'client_id': client_id
        };
        $.ajax({
            url: "generate/invoice",
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

    $('body').on('click', '.voidinvoice', function () {
        
        var invoice_id = $(this).attr('data-invoiceid');
        var id = $(this).attr('data-id');
        var response = confirm("Are You sure want to void the Invoice?");

        if (response == true) {
            var dataToSend = {
                'invoice_id': invoice_id,
                'id': id,
            };

            $.ajax({
                url: "void/invoice",
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
        }
        
    })
   
  });

</script>
@stop