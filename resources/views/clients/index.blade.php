@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">

        <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewClient"> Create New Client</a>

        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>Client Name</th>
                    <th>Address</th>
                    <th>Rate</th>
                    <th>OT Rate</th>
                    <th>ABN</th>
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
                <form id="clientForm" name="clientForm" class="form-horizontal">

                   <input type="hidden" name="client_id" id="client_id">

                    <div class="form-group">
                        <label for="client_name" class="col-sm-6 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="client_name" name="client_name" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-6 control-label">Address</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="address" name="address" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rate_per_hour" class="col-sm-6 control-label">Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="rate_per_hour" name="rate_per_hour" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ot_rate_per_hour" class="col-sm-6 control-label">OT Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="ot_rate_per_hour" name="ot_rate_per_hour" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="abn" class="col-sm-6 control-label">ABN</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="abn" name="abn" value="" required="">
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
        ajax: "{{ route('clients.index') }}",

        columns: [
            {data: 'client_name', name: 'client_name'},
            {data: 'address', name: 'address'},
            {data: 'rate_per_hour', name: 'rate_per_hour'},
            {data: 'ot_rate_per_hour', name: 'ot_rate_per_hour'},
            {data: 'abn', name: 'abn'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "columnDefs": [
            { "width": "10%", "targets": [2,3] },
            { "width": "25%", "targets": [1] }
        ],
        order: [[0, 'asc']]
    });

    $('#createNewClient').click(function () {
        $('#saveBtn').val("create-client");
        $('#client_id').val('');
        $('#clientForm').trigger("reset");

        $('#modelHeading').html("Create New Client");
        $('#ajaxModel').modal('show');

    });

    $('body').on('click', '.editClient', function () {

      var client_id = $(this).data('id');
        console.log('edit');
      $.get("{{ route('clients.index') }}" +'/' + client_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Client");
          $('#saveBtn').val("edit-client");
          $('#ajaxModel').modal('show');

          $('#client_id').val(data.id);
          $('#client_name').val(data.client_name);
          $('#address').val(data.address);
          $('#rate_per_hour').val(data.rate_per_hour);
          $('#ot_rate_per_hour').val(data.ot_rate_per_hour);
          $('#abn').val(data.abn);
      })

    });

      
    $('#saveBtn').click(function (e) {

        e.preventDefault();

        $(this).html('Saving..');

        $.ajax({
          data: $('#clientForm').serialize(),
          url: "{{ route('clients.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            $('#clientForm').trigger("reset");
            $('#ajaxModel').modal('hide');
            table.draw();
            $('#saveBtn').html('Save Changes');
            toastr.success('Client saved successfully!');
          },
          error: function (data) {
            console.log('Error:', data);
            $('#saveBtn').html('Save Changes');
            toastr.error('Error Saving!');

          }
      });
    });


    $('body').on('click', '.deleteClient', function () {

        var client_id = $(this).data("id");

        var response = confirm("Are You sure want to delete?");

        if (response == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('clients.store') }}"+'/'+client_id,

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
  });

</script>
@stop