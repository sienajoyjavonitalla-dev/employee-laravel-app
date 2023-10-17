@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">

        <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewClient"> Create New Client</a>

        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>Rate</th>
                    <th>OT Rate</th>
                    <th>Travel Allowance</th>
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
                @csrf

                   <input type="hidden" name="client_id" id="client_id">
                   <div class="form-group">
                        <label for="client_name" class="col-sm-6 control-label">Company Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="company_name" name="company_name" value="" maxlength="50" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="POBOX_AddressLine1" class="col-sm-6 control-label">AddressLine</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="POBOX_AddressLine1" name="POBOX_AddressLine1" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="POBOX_City" class="col-sm-6 control-label">City</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="POBOX_City" name="POBOX_City" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="POBOX_Region" class="col-sm-6 control-label">Region</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="POBOX_Region" name="POBOX_Region" value="" maxlength="50">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="POBOX_PostalCode" class="col-sm-6 control-label">PostalCode</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="POBOX_PostalCode" name="POBOX_PostalCode" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="POBOX_Country" class="col-sm-6 control-label">Country</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="POBOX_Country" name="POBOX_Country" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="PhoneNumber" class="col-sm-6 control-label">Phone Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="PhoneAreaCode" class="col-sm-6 control-label">Phone Area Code</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="PhoneAreaCode" name="PhoneAreaCode" value="" maxlength="50">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rate_per_hour" class="col-sm-6 control-label">Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="rate_per_hour" name="rate_per_hour" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="other_rate_per_hour" class="col-sm-6 control-label">Other Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="other_rate_per_hour" name="other_rate_per_hour" value="" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ot_rate_per_hour" class="col-sm-6 control-label">OT Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="ot_rate_per_hour" name="ot_rate_per_hour" value="" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="holiday_rate" class="col-sm-6 control-label">Holiday Rate</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="holiday_rate" name="holiday_rate" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ot_rate_per_hour" class="col-sm-6 control-label">Travel Allowance</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="travel_allowance" name="travel_allowance" value="" required="">
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
        pageLength: 8,
        ajax: "{{ route('clients.index') }}",

        columns: [
            {data: 'company_name', name: 'company_name'},
            {data: 'complete_address', name: 'complete_address'},
            {data: 'rate_per_hour', name: 'rate_per_hour'},
            {data: 'ot_rate_per_hour', name: 'ot_rate_per_hour'},
            {data: 'travel_allowance', name: 'travel_allowance'},
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
      $.get("{{ route('clients.index') }}" +'/' + client_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Client");
          $('#saveBtn').val("edit-client");
          $('#ajaxModel').modal('show');

          $('#client_id').val(data.id);
          $('#client_name').val(data.client_name);
          $('#address').val(data.address);
          $('#rate_per_hour').val(data.rate_per_hour);
          $('#other_rate_per_hour').val(data.other_rate_per_hour);
          $('#ot_rate_per_hour').val(data.ot_rate_per_hour);
          $('#abn').val(data.abn);
          $('#company_name').val(data.company_name);
          $('#travel_allowance').val(data.travel_allowance);
          $('#holiday_rate').val(data.holiday_rate);
          $('#POBOX_AddressLine1').val(data.POBOX_AddressLine1);
          $('#POBOX_City').val(data.POBOX_City);
          $('#POBOX_Region').val(data.POBOX_Region);
          $('#POBOX_PostalCode').val(data.POBOX_PostalCode); 
          $('#POBOX_Country').val(data.POBOX_Country);
          $('#PhoneNumber').val(data.PhoneNumber);
          $('#PhoneAreaCode').val(data.PhoneAreaCode);
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
                    toastr.error('Error!');

                }
            });
        }
    });
  });

</script>
@stop