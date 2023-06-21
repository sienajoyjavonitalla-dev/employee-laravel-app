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
                    <th>Company</th>
                    <th>Status</th>
                    <th>Address</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Assigned Count</th>
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
                            <!-- <input type="text" class="form-control" id="client_id" name="client_id" value="" maxlength="50" required=""> -->
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label for="employee_id" class="col-sm-6 control-label">Assign to</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="employee_id" id="employee_id" required="">
                                <option value="">-- Select --</option>
                                @foreach ($assigned as $key => $value)
                                    <option value="{{ $key }}"> 
                                        {{ $value }} 
                                    </option>
                                @endforeach    
                            </select>
                        </div>
                    </div> -->

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
                        <label for="no_of_persons" class="col-sm-6 control-label">Number of Persons Assigned</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="no_of_persons" name="no_of_persons" value="" required="">
                        </div>
                    </div>

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
                            <input type="text" class="form-control" id="job_title" name="job_title" value="" >
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
        ajax: "{{ route('jobs.index') }}",

        columns: [
            {data: 'id', name: 'id'},
            {data: 'client', name: 'client'},
            {data: 'status', name: 'status'},
            {data: 'address', name: 'address'},
            {data: 'start_date_time', name: 'start_date_time'},
            {data: 'end_date_time', name: 'end_date_time'},
            {data: 'no_of_persons', name: 'no_of_persons'},
            {data: 'assigned', name: 'assigned'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
            { "width": "20%", "targets": [7] },
            { "width": "15%", "targets": [3, 8] },
        ],
        order: [[4, 'desc']]
    });

    

    $('#createNewJob').click(function () {
        $('#saveBtn').val("create-job");
        $('#job_id').val('');
        $('#jobForm').trigger("reset");

        $('#modelHeading').html("Create New Job");
        $('#ajaxModel').modal('show');

    });

    $('body').on('click', '.editJob', function () {

      var job_id = $(this).data('id');
        console.log('edit');
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
          $('#no_of_persons').val(data.no_of_persons);
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

    $('#saveBtn').click(function (e) {

        e.preventDefault();

        $(this).html('Saving..');

        $.ajax({
          data: $('#jobForm').serialize(),
          url: "{{ route('jobs.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            $('#jobForm').trigger("reset");
            $('#ajaxModel').modal('hide');
            table.draw();
            $('#saveBtn').html('Save Changes');
            toastr.success('Job saved successfully!');
          },
          error: function (data) {
            console.log('Error:', data);
            $('#saveBtn').html('Save Changes');
            toastr.error('Error Saving!');

          }
      });
    });

    $('#saveAssBtn').click(function (e) {

        e.preventDefault();

        $(this).html('Saving..');

        $.ajax({
        data: $('#assignForm').serialize(),
        url: "{{ route('assign.store') }}",
        type: "POST",
        dataType: 'json',
        success: function (data) {
            $('#assignForm').trigger("reset");
            $('#assignModal').modal('hide');
            $('#saveAssBtn').html('Save Changes');

            if(data.success) {
                table.draw();
                toastr.success('Assigned Job successfully!');
            } else {
                toastr.error('Job Assigned is full already!');

            }
            
        },
        error: function (data) {
            console.log('Error:', data);
            $('#saveAssBtn').html('Save Changes');
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
                    console.log('Error:', data);
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
                    console.log('Error:', data);
                    toastr.error('Error!');
                }
            });
        }
        });
  });

</script>
@stop