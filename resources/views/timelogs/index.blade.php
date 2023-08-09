@extends('layouts.app')


@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">
        
        <div class="row">
            <div class="col-lg-6">
                <strong>Date Filter </strong>
                <input type="text" name="daterange" value="" />
            </div>
            <div class="col-lg-6">
                <strong>Job ID </strong>
                <select class="selectpicker" name="job" id="job">
                    <option value="">-- Select --</option>
                    @foreach ($jobs as $j)
                        <option value="{{ $j->id }}"> 
                            Job #{{ $j->id . ' - '. $j->company_name}} 
                        </option>
                    @endforeach       
                </select>
            </div>
        </div>
        @if(auth()->user()->roles == 'admin' || auth()->user()->roles == 'subadmin')

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
                    @foreach ($filter_assigned as $key => $value)
                        <option value="{{ $key }}"> 
                            {{ $value}} 
                        </option>
                    @endforeach    
                </select>
            </div>
            
        </div>
        <br/>
            @if(auth()->user()->roles == 'admin')
            <a href="javascript:void(0)" class="btn btn-danger generate" id="generate_btn"><i class="fas fa-print"></i> Generate Timesheet</a>
            @endif
        <a class="btn btn-success mt-4 mb-4" href="javascript:void(0)" id="createNewTimeLog"> Create New TimeLog</a>
        @endif

        <button class="w-auto mb-4 mt-4 btn btn-primary filter"><i class="fas fa-search"></i> Filter</button>

        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th></th>
                    <th>Job ID</th>
                    <th>Client</th>
                    <th>Assigned</th>
                    <th>Lunch Break</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Date</th>
                    <th>Attachment</th>
                    <th>Notes</th>
                    <th width="280px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        </div>
    </div>
</div>
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>

            <div class="modal-body">
                <form id="timelogForm" name="timelogForm" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                   <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="job_id" class="col-sm-6 control-label">Job</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="job_id" id="job_id" required="">
                                <option value="">-- Select --</option>
                                @foreach ($jobs as $j)
                                    <option value="{{ $j->id }}"> 
                                        Job #{{ $j->id . ' - '. $j->company_name}} 
                                    </option>
                                @endforeach    
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assigned_id" class="col-sm-6 control-label">Assign to</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="assigned_id" id="assigned_id" required="">
                                <option value="">-- Select --</option>
                                @foreach ($users as $key => $value)
                                    <option value="{{ $key }}"> 
                                        {{ $value }} 
                                    </option>
                                @endforeach    
                            </select>
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label for="start_time" class="col-sm-6 control-label">Start Time</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="start_time" name="start_time" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="end_time" class="col-sm-6 control-label">End Time</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="end_time" name="end_time" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="date" class="col-sm-6 control-label">Date</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="date" name="date" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lunch_break" class="col-sm-6 control-label">Lunch Break</label>
                        <input class="form-check-input" type="checkbox" id="lunch_break" name="lunch_break" value=""  checked="false">
                    </div>

                    <div class="form-group">
                        <label for="timelog_notes" class="col-sm-6 control-label">Notes</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="timelog_notes" name="timelog_notes" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="timesheet" class="col-sm-6 control-label">Timesheet</label>
                        <div class="col-sm-12">
                            <input type="file" id="timesheet" name="timesheet" value="">
                        </div>
                    </div>
                    <div class="form-group col-sm-10">Uploaded file: <input type="text" disabled id="existingTimesheet" value=""></div>
                    
                    <div class="form-group">
                        <label for="authorized" class="col-sm-6 control-label">Authorized Person</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="authorized" name="authorized" value="" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signature" class="col-sm-6 control-label">Upload Signature</label>
                        <div class="col-sm-12">
                            <input type="file" id="signature" name="signature" value="">
                        </div>
                    </div>
                    <div class="form-group col-sm-10">Uploaded file: <input type="text" disabled id="existingSignature" value=""></div>
                    

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save</button>
                    </div>
                </form>
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

    load_data($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'), 
        $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'), 
        null,null,null);

    function load_data(from_date, to_date, client, job, assigned) {

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 8,
            ajax: {

                url: "{{ route('timelogs.index') }}",
                data:{
                    from_date: from_date, 
                    to_date: to_date,
                    client: client,
                    job:job,
                    assigned: assigned,
                }
            },
            columns: [
                {
                    'className':      'details-control',
                    'orderable':      false,
                    'data':           null,
                    'defaultContent': ''
                },
                {data: 'job_id', name: 'job_id'},
                {data: 'company_name', name: 'company_name'},
                {data: 'name', name: 'name'},
                {data: 'with_lunch', name: 'with_lunch'},
                {data: 'start_time', name: 'start_time'},
                {data: 'end_time', name: 'end_time'},
                {data: 'date', name: 'date'},
                {data: 'timesheet_url', name: 'timesheet_url'},
                {data: 'notes', name: 'notes'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "columnDefs": [
                { "width": "15%", "targets": [9] },
                { "width": "12%", "targets": [ 10] },

            ],
            order: [[7, 'asc']]
        });
    }

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

    $('.generate').click(function (e) {
        var from_date = ($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'));
        var to_date = ($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'));
        var client = $('#client').val();
        var job = $('#job').val();
        var assigned = $('#assigned').val();

        var uri = "/generateTimesheet?assigned="+ assigned +"&job="+job+"&from_date="+from_date+"&to_date="+to_date;
        
        var encoded = encodeURI(uri);
        window.location.href=encoded;
      
    })

    $('#createNewTimeLog').click(function () {
        $('#saveBtn').val("create-timelog");
        $('#id').val('');
        $('.job_container').hide();
        $('#job_id').attr('disabled', false); 
        $('#assigned_id').attr('disabled', false); 


        $('#timelogForm').trigger("reset");

        $('#modelHeading').html("Create New Timelog");
        $('#ajaxModel').modal('show');

    });

    function format(d) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
            '<tr>'+
                '<td>Subcontractor Invoice ID:</td>'+
                '<td>'+d.subbies_invoice_id+'</td>'+
            '</tr>'+
            // '<tr>'+
            //     '<td>Extra info:</td>'+
            //     '<td>And any further details here (images etc)...</td>'+
            // '</tr>'+
        '</table>';
    }
    
    // Add event listener for opening and closing details
    $('.data-table tbody').on('click', 'td.details-control', function(){
        var tr = $(this).closest('tr');
        var row = $('.data-table').DataTable().row( tr );
        if(row.child.isShown()){
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });
    $('body').on('click', '.editTimeLog', function () {
        $('#job_id').attr('disabled', true); 
        $('#assigned_id').attr('disabled', true); 
        if( '{{Auth::user()->roles}}' != 'admin' && '{{Auth::user()->roles}}' != 'subadmin' ) {
            $('#assigned_id').attr('disabled', true); 
            $('#start_time').attr('disabled', true); 
            $('#end_time').attr('disabled', true); 
            $('#date').attr('disabled', true); 
        }
        var timelogs = $(this).data('id');
        $.get("{{ route('timelogs.index') }}" +'/' + timelogs +'/edit', function (data) {
          $('#modelHeading').html("Edit TimeLog");
          $('#saveBtn').val("edit-timelog");
          $('#ajaxModel').modal('show');
          $('.job_container').show();
          $('#id').val(data.id);
          $('#job_id').val(data.job_id);
          $('#assigned_id').val(data.assigned_id);
          $('#start_time').val(data.start_time);
          $('#end_time').val(data.end_time);
          $('#date').val(data.date);
          $('#existingTimesheet').val(data.timesheet);
          $('#authorized').val(data.authorized);
          $('#existingSignature').val(data.signature);
          $('#timelog_notes').val(data.notes);

          if(data.lunch_break == 1) {
            $('#lunch_break').prop('checked',true);
          } else {
            $('#lunch_break').prop('checked',false);
          }

      })

    });

      
    $('#saveBtn').click(function (e) {

        $('#job_id').attr('disabled', false); 
        $('#assigned_id').attr('disabled', false); 

        if( '{{Auth::user()->roles}}' != 'admin' && '{{Auth::user()->roles}}' != 'subadmin' ) {
            $('#assigned_id').attr('disabled', false); 
            $('#start_time').attr('disabled', false); 
            $('#end_time').attr('disabled', false); 
            $('#date').attr('disabled', false); 
        }

        var formData = new FormData($('#timelogForm')[0]);
        e.preventDefault();

        $(this).html('Saving..');
        $.ajax({
            url: "{{ route('timelogs.store') }}",
            type: "POST",
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                $('#timelogForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                $('.data-table').DataTable().draw();
                $('#saveBtn').html('Save Changes');

                toastr.success('Log saved successfully!');
            },
            error: function (data) {
                $('#saveBtn').html('Save Changes');
                toastr.error('Error Saving!');

            }
        });
    });


    $('body').on('click', '.deleteTimeLog', function () {

        var timelog = $(this).data("id");

        var response = confirm("Are You sure want to delete?");

        if (response == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('timelogs.store') }}"+'/'+timelog,

                success: function (data) {
                    // table.draw();
                    $('.data-table').DataTable().draw();

                    toastr.success('Deleted successfully!');

                },
                error: function (data) {
                    toastr.error('Error!');

                }
            });
        }
    });

    $('#lunch_break').change(function() {
        $('#lunch_break').val(this.checked);        
    });
  });

</script>
@stop