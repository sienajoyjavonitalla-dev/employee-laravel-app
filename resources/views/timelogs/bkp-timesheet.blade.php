@extends('layouts.app')

@section('content')
<div class="card p-3 shadow" >
        <div class="tab-pane fade active show" id="nav-invoice" role="tabpanel" aria-labelledby="nav-invoice-tab">
            <nav>
                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-generate-tab" data-bs-toggle="tab" data-bs-target="#nav-generate" type="button" role="tab" aria-controls="nav-generate" aria-selected="true">Timesheet</button>
                </div>
            </nav>
            <div class="tab-content p-3 border bg-light" id="nav-tabContent">
                <div class="tab-pane fade active show" id="nav-generate" role="tabpanel" aria-labelledby="nav-generate-tab">
                <div class="row">
                        <div class="col-lg-12 align-items-center p-4">
                            <div class="container">
                            <div class="row">
                                <div class="col-lg-6">
                                    <strong>Date Filter </strong>
                                    <input type="text" name="daterange" value="" />
                                </div>
                                <div class="col-lg-6">
                                    <strong>User Type </strong>
                                    <select class="selectpicker" name="user_type" id="user_type">
                                        <option value="">-- Select --</option>
                                            <option value="full-timer"> Full Timer </option>
                                            <option value="subcontractor"> Subcontractor </option>
                                    </select>
                                </div>
                                
                                
                            </div>
                            <div class="row mt-3">
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
                                <div class="col-lg-6">
                                    <strong>Assigned Person</strong>
                                    <select  name="assigned" id="assigned">
                                        <option value="">-- Select --</option>
                                        @foreach ($filter_assigned as $d)
                                            <option value="{{ $d->assigned_id }}"> 
                                                {{ $d->name}} 
                                            </option>
                                        @endforeach    
                                    </select>
                                </div>
                                
                            </div>
                            <div class="row mt-3">
                                <div class="col-lg-6">
                                    <strong>Job ID </strong>
                                    <select class="selectpicker" name="job" id="job">
                                        <option value="">-- Select --</option>
                                        @foreach ($jobs as $key => $value)
                                            <option value="{{ $key }}"> 
                                                {{ $key }} 
                                            </option>
                                        @endforeach    
                                    </select>
                                </div>
                                
                            </div>
                            <button class="w-auto mb-4 mt-4 btn btn-success filter"><i class="fas fa-search"></i> Filter</button>

                            <table class="table table-bordered table-hover data-table" width="100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Job</th>
                                        <th>Site</th>
                                        <th>Client</th>
                                        <th>Assigned Person</th>
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

    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        startDate: moment().subtract(1, 'M'),
        endDate: moment()
    });

    $('#client').val('');
    $('#job').val('');
    $('#assigned').val('');
    $('#user_type').val('');

    load_data($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'), 
    $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'), 
    null,null,null, null);

    function load_data(from_date, to_date, client, job, assigned, user_type) {
        var table = $('.data-table').DataTable({
            dom: 'lBfrtip',
            buttons: {
                buttons:[
                    'copy', 'csv'
                ],
                dom: {
                    button: { className: "btn btn-primary ml-1"},
                }
            },
            processing: true,
            serverSide: true,
            pageLength: 8,
            ajax: {

                url: "{{ route('timesheet', ['name'=>'generate']) }}",
                data:{
                    from_date: from_date, 
                    to_date: to_date,
                    client: client,
                    job:job,
                    assigned: assigned,
                    user_type: 'full-timer'
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'address', name: 'address'},
                {data: 'company_name', name: 'company_name'},
                {data: 'name', name: 'name'},
                {data: 'job_title', name: 'job_title'},
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
        var job = $('#job').val();
        var assigned = $('#assigned').val();
        var user_type = $('#user_type').val();


        if(from_date != '' &&  to_date != '')
        {
            $('.data-table').DataTable().destroy();
            load_data(from_date, to_date, client, job, assigned, user_type);
        }
        else
        {
            toastr.error('Both Date is required!');
        }
    });

  });

</script>
@stop