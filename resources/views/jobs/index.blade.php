@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">

        <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewJob"> Create New Job</a>

        <table class="table table-bordered table-hover data-table">
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
                    <th>Action</th>
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
                <h4 class="modal-title" id="modelHeading">Edit</h4>
            </div>

            <div class="modal-body">
                <form id="jobForm" name="jobForm" class="form-horizontal">
                @csrf

                   <input type="hidden" name="job_id" id="job_id">

                    <div class="form-group">
                        <label for="client_id" class="col-sm-6 control-label">Company Name</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="client_id" id="client_id" required="">
                                <option value="">-- Select --</option>
                                @foreach ($clients as $key => $value)
                                    <option value="{{ $key }}"> 
                                        {{ $value }} 
                                    </option>
                                @endforeach    
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-6 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="po_number" class="col-sm-6 control-label">PO Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="po_number" name="po_number" value="" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-6 control-label">Site Address</label>
                        <div class="col-sm-12">
                            <textarea id="address" name="address" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="start_date_time" class="col-sm-6 control-label">Start Date</label>
                        <div class="col-sm-12">
                            <input type="date" class="form-control" id="start_date_time" name="start_date_time" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="end_date_time" class="col-sm-6 control-label">End Date</label>
                        <div class="col-sm-12">
                            <input type="date" class="form-control" id="end_date_time" name="end_date_time" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="timesheet" class="col-sm-6 control-label">Timesheet</label>
                        <div class="col-sm-12">
                            <input type="file" class="form-control" id="timesheet" name="timesheet" value="" >
                        </div>
                    </div>

                    <div class="form-group col-sm-10">Uploaded file: <input type="text" disabled id="existingTimesheet" value=""></div>
                    

                    <!-- <div class="form-group">
                        <label for="job_assignees" class="col-sm-6 control-label">Assign to</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="job_assignees[]" id="job_assignees" multiple="multiple">
                                <option value="">-- Select --</option>
                                @foreach ($assigned as $key => $value)
                                    <option value="{{ $key }}"> 
                                        {{ $value }} 
                                    </option>
                                @endforeach    
                            </select>
                        </div>
                    </div> -->
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading">Assign</h4>
            </div>

            <div class="modal-body">
                <form id="assignForm" name="assignForm" class="form-horizontal pb-1">
                @csrf

                    <div class="form-group">
                        <input type="hidden" class="form-control" id="job_assignee_id" name="job_assignee_id" value="" >

                        <label for="assigned_id" class="col-sm-6 control-label">Assign to</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="assigned_id" id="assigned_id" required="">
                                <option value="">-- Select --</option>
                                @foreach ($assigned as $key => $value)
                                    <option value="{{ $key }}"> 
                                        {{ $value }} 
                                    </option>
                                @endforeach    
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="job_title" class="col-sm-6 control-label">Job Title</label>
                        <div class="col-sm-12">
                            <!-- <input type="text" class="form-control" id="job_title" name="job_title" value="" > -->

                            <select class="form-control" name="job_title" id="job_title" required="">
                                <option value="">-- Select --</option>
                                <option value="boiler maker">Boiler Maker</option>
                                <option value="operator">Operator (applies 2nd rate)</option>
                                <option value="dogman">Dogman</option>
                                <option value="rigger">Rigger</option>
                                <option value="spotter">Spotter</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveAssBtn" value="create">Add</button>
                    </div>
                </form>

                <table class="table table-bordered table-hover assign-table" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Assigned</th>
                            <th>Job Title</th>
                            <th>Action</th>
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

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 8,
        responsive: true,
        ajax: "{{ route('jobs.index', ['name'=>'list']) }}",

        columns: [
            {data: 'id', name: 'id'},
            {data: 'invoice', name: 'invoice'},
            {data: 'client', name: 'client'},
            {data: 'stat_change', name: 'stat_change'},
            {data: 'address', name: 'address'},
            {data: 'start_date_time', name: 'start_date_time'},
            {data: 'end_date_time', name: 'end_date_time'},
            {data: 'assigned', name: 'assigned'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
            { "width": "20%", "targets": [7, 8] },
            { "width": "15%", "targets": [4] },
            { "width": "10%", "targets": [3] },
        ],
        order: [[5, 'desc']]
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

    $('#createNewJob').click(function () {
        $('#saveBtn').val("create-job");
        $('#job_id').val('');
        $('#jobForm').trigger("reset");

        $('#modelHeading').html("Create New Job");
        $('#ajaxModel').modal('show');

    });

    $('body').on('click', '.editJob', function () {

      var job_id = $(this).data('id');
      $.get("{{ route('jobs.index') }}" +'/' + job_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Job");
          $('#saveBtn').val("edit-job");
          $('#ajaxModel').modal('show');

          $('#job_id').val(data.id);
          $('#client_id').val(data.client_id);
          $('#employee_id').val(data.employee_id);
          $('#title').val(data.title);
          $('#po_number').val(data.po_number);
          $('#description').val(data.description);
          $('#address').val(data.address);
          $('#start_date_time').val(data.start_date_time);
          $('#end_date_time').val(data.end_date_time);
          $('#start_time').val(data.start_time);
          $('#end_time').val(data.end_time);
          $('#existingTimesheet').val(data.timesheet);

      })

    });

    $('body').on('click', '.assignBtn', function () {
        $('#assignModal').modal('show');
        var job_id = $(this).data('id');
        $('#job_assignee_id').val(job_id);

        var url = "{{ route('assign.index', ['job'=>':id']) }}";
        url = url.replace(':id', job_id);
        var assign_table = $('.assign-table').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            ajax: url,
            columns: [
                {data: 'name', name: 'name'},
                {data: 'job_title', name: 'job_title'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            "columnDefs": [
            ]
        });
        assign_table.destroy();

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
            paging: false,
            // pageLength: 4,
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

    $('body').on('click', '.completeBtn', function () {
        var job_id = $(this).data('id');
        var response = confirm("Are You sure want to complete the Job?");

        if (response == true) {
            $.ajax({
                data:{
                    job_id: job_id, 
                },
                url: "{{ route('complete.job') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    if(data.success) {
                        table.draw();
                        toastr.success(data.success, 'SUCCESS');
                    } else {
                        toastr.error(data.success, 'ERROR');

                    }
                    
                },
                error: function (data) {
                    toastr.error('Error Saving!');
                }
            });
        }
    });

    $('#saveBtn').click(function (e) {
        var formData = new FormData($('#jobForm')[0]);

        e.preventDefault();

        $(this).html('Saving..');

        $.ajax({
            data: formData,
            url: "{{ route('jobs.store') }}",
            type: "POST",
            dataType: 'json',
            processData: false,
            contentType: false, 
            success: function (data) {
                $('#jobForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                table.draw();
                $('#saveBtn').html('Save Changes');
                toastr.success('Job saved successfully!');
            },
            error: function (data) {
                $('#saveBtn').html('Save Changes');
                toastr.error('Error Saving!');

            }
      });
    });

    $('#saveAssBtn').click(function (e) {

        e.preventDefault();

        if( $('#assigned_id').val() == '' )
        {
            toastr.error('Cannot assign empty user.');
            return;
        }

        $(this).html('Saving..');

        $.ajax({
            data: $('#assignForm').serialize(),
            url: "{{ route('assign.store') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('#assignForm').trigger("reset");
                $('#assignModal').modal('hide');
                $('#saveAssBtn').html('Add');

                if(data.success) {
                    table.draw();
                    toastr.success('Assigned Job successfully!');
                } else {
                    toastr.error('Job Assigned is full already!');

                }
                
            },
            error: function (data) {
                console.log('Error:', data);
                $('#saveAssBtn').html('Add');
                toastr.error('Error Saving!');

            }
        });
    });

    $('body').on('click', '.deleteJob', function () {

        var job_id = $(this).data("id");

        var response = confirm("Are You sure want to delete?");

        if (response == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('jobs.store') }}"+'/'+job_id,

                success: function (data) {
                    table.draw();
                    toastr.success('Deleted successfully!');

                },
                error: function (data) {
                    toastr.error('Error!');

                }
            });
        }
    });
    $('body').on('click', '.deleteJobAssign', function () {

        var job_id = $(this).data("id");

        var response = confirm("Are You sure want to delete?");

        if (response == true) {
            var url = "{{ route('assign.delete', ['assign'=>':id']) }}";
            url = url.replace(':id', job_id);

            $.ajax({
                type: "POST",
                url: url,
                success: function (data) {
                    table.draw();
                    $('#assignModal').modal('hide');
                    toastr.success('Deleted successfully!');
                },
                error: function (data) {
                    toastr.error('Error!');
                }
            });
        }
    });
  });

</script>
@stop