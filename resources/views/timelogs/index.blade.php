@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">

        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>Job ID</th>
                    <th>Assigned</th>
                    <th>Lunch Break</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Date</th>
                    <th>Timesheet</th>
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
                   <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="job_id" class="col-sm-6 control-label">Job</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="job_id" name="job_id" value="" maxlength="50" required="" disabled="true">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="assigned_id" class="col-sm-6 control-label">Assign to</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="assigned_id" name="assigned_id" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lunch_break" class="col-sm-6 control-label">Lunch Break</label>
                        <input class="form-check-input" type="checkbox" id="lunch_break" name="lunch_break" value=""  checked="false">
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
                        <label for="timesheet" class="col-sm-6 control-label">Timesheet</label>
                        <div class="col-sm-12">
                            <input type="file" id="timesheet" name="timesheet" value="">
                        </div>
                    </div>
                    <div class="form-group col-sm-10">Uploaded file: <input type="text" disabled id="existingTimesheet" value=""></div>
                    
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

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 8,
        ajax: "{{ route('timelogs.index') }}",

        columns: [
            {data: 'job_id', name: 'job_id'},
            {data: 'assigned_to', name: 'assigned_to'},
            {data: 'with_lunch', name: 'with_lunch'},
            {data: 'start_time', name: 'start_time'},
            {data: 'end_time', name: 'end_time'},
            {data: 'date', name: 'date'},
            {data: 'timesheet', name: 'timesheet'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
            { "width": "20%", "targets": [2] },
            { "width": "10%", "targets": [4,5] }

        ],
        order: [[6, 'asc']]
    });

    $('body').on('click', '.editTimeLog', function () {

      var timelogs = $(this).data('id');
      $.get("{{ route('timelogs.index') }}" +'/' + timelogs +'/edit', function (data) {
          $('#modelHeading').html("Edit TimeLog");
          $('#saveBtn').val("edit-timelog");
          $('#ajaxModel').modal('show');

          $('#id').val(data.id);
          $('#job_id').val(data.job_id);
          $('#assigned_id').val(data.assigned_id);
          $('#start_time').val(data.start_time);
          $('#end_time').val(data.end_time);
          $('#date').val(data.date);
          $('#existingTimesheet').val(data.timesheet);

          if(data.lunch_break == 1) {
            $('#lunch_break').prop('checked',true);
          } else {
            $('#lunch_break').prop('checked',false);
          }

      })

    });

      
    $('#saveBtn').click(function (e) {
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
                table.draw();
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
                    table.draw();
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