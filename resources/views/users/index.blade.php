@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">
        <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewUser"> Create New User</a>

        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>Name</th>
                    <th>ABN</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Rate</th>
                    <th>OT Rate</th>
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
                <form id="userForm" name="userForm" class="form-horizontal">
                @csrf

                   <input type="hidden" name="user_id" id="user_id">

                    <div class="form-group">
                        <label for="name" class="col-sm-6 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="abn" class="col-sm-6 control-label">ABN</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="abn" name="abn" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-6 control-label">Email</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="email" name="email" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-sm-6 control-label">Password</label>
                        <div class="col-sm-12">
                            <input type="password" class="form-control" id="password" name="password" value="" maxlength="50" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="roles" class="col-sm-6 control-label">Role</label>
                        <div class="col-sm-12">
                            <select  class="form-control" name="roles" id="roles" required="">
                                <option value="">-- Select --</option>
                                <option value="subcontractor"> Subcontractor</option>
                                <option value="full-timer"> Full Timer</option>
                                <option value="admin"> Admin</option>
                                <option value="subadmin"> Subadmin</option>
                            </select>
                            <!-- <input type="text" class="form-control" id="roles" name="roles" value="" maxlength="50" required=""> -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rate_per_hour" class="col-sm-6 control-label">Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="rate_per_hour" name="rate_per_hour" value="" maxlength="50" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="other_rate_per_hour" class="col-sm-6 control-label">Other Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="other_rate_per_hour" name="other_rate_per_hour" value="" maxlength="50" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ot_rate_per_hour" class="col-sm-6 control-label">OT Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="ot_rate_per_hour" name="ot_rate_per_hour" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="travel_allowance" class="col-sm-6 control-label">Travel Allowance</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="travel_allowance" name="travel_allowance" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gst" class="col-sm-6 control-label">GST</label>
                        <div class="col-sm-12">
                            <select  class="form-control" name="gst" id="gst">
                                <option value="">-- Select --</option>
                                <option value="1"> Yes</option>
                                <option value="0"> No</option>
                            </select>
                            <!-- <input type="text" class="form-control" id="roles" name="roles" value="" maxlength="50" required=""> -->
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="manageFilesModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading">User Files</h4>
            </div>

            <div class="modal-body">
                <form id="filesForm" name="filesForm" class="form-horizontal pb-1">
                @csrf

                    <div class="form-group">
                        <input type="hidden" class="form-control" id="user_id" name="user_id" value="" >

                        <label for="file" class="col-sm-6 control-label">File</label>
                        <div class="col-sm-12">
                            <input type="file" id="timesheet" name="timesheet" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="col-sm-6 control-label">Description</label>
                        <div class="col-sm-12">
                            <textarea id="description" name="description" class="form-control"></textarea>
                        </div>
                    </div>   

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Add</button>
                    </div>
                </form>

                <table class="table table-bordered table-hover files-table" width="100%">
                    <thead class="thead-light">
                        <tr>
                            <th>File</th>
                            <th>Description</th>
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
        pageLength: 8,
        ajax: "{{ route('users.index') }}",

        columns: [
            {data: 'name', name: 'name'},
            {data: 'abn', name: 'abn'},
            {data: 'email', name: 'email'},
            {data: 'roles', name: 'roles'},
            {data: 'rate_per_hour', name: 'rate_per_hour'},
            {data: 'ot_rate_per_hour', name: 'ot_rate_per_hour'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
        ],
        order: [[1, 'asc']]
    });

    $('body').on('click', '.manageFiles', function () {
        $('#manageFilesModal').modal('show');
        var user_id = $(this).data('id');
        $('#user_id').val(user_id);

        // var url = "{{ route('assign.index', ['job'=>':id']) }}";
        // url = url.replace(':id', job_id);
        // var assign_table = $('.assign-table').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     paging: false,
        //     ajax: url,
        //     columns: [
        //         {data: 'name', name: 'name'},
        //         {data: 'job_title', name: 'job_title'},
        //         {data: 'action', name: 'action', orderable: false, searchable: false},
        //     ],
        //     "columnDefs": [
        //     ]
        // });
        // assign_table.destroy();

    });

    $('#createNewUser').click(function () {
        $('#saveBtn').val("create-user");
        $('#user_id').val('');
        $('#userForm').trigger("reset");

        $('#modelHeading').html("Create New User");
        $('#ajaxModel').modal('show');

    });

    $('body').on('click', '.editUser', function () {

      var user_id = $(this).data('id');
      $.get("{{ route('users.index') }}" +'/' + user_id +'/edit', function (data) {
          $('#modelHeading').html("Edit User");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');

          $('#user_id').val(data.id);
          $('#name').val(data.name);
          $('#abn').val(data.abn);
          $('#email').val(data.email);
          $('#roles').val(data.roles);
          $('#rate_per_hour').val(data.rate_per_hour);
          $('#other_rate_per_hour').val(data.other_rate_per_hour);
          $('#ot_rate_per_hour').val(data.ot_rate_per_hour);
          $('#travel_allowance').val(data.travel_allowance);
          $('#gst').val(data.gst);

      })

    });

      
    $('#saveBtn').click(function (e) {

        e.preventDefault();

        $(this).html('Saving..');

        $.ajax({
          data: $('#userForm').serialize(),
          url: "{{ route('users.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            $('#userForm').trigger("reset");
            $('#ajaxModel').modal('hide');
            table.draw();
            $('#saveBtn').html('Save Changes');
            toastr.success('User saved successfully!');
          },
          error: function (data) {

            $('#saveBtn').html('Save Changes');
            toastr.error('Error Saving!');

          }
        });
    });


    $('body').on('click', '.deleteUser', function () {

        var user_id = $(this).data("id");

        var response = confirm("Are You sure want to delete !");

        if(response == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('users.store') }}"+'/'+user_id,

                success: function (data) {
                    table.draw();
                    toastr.success(data.responseText);

                },
                error: function (data) {
                    toastr.error(data.responseText);
                }
            });
        }
    });
  });

</script>
@stop