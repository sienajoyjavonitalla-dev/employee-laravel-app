@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center p-4">
        <div class="container">
        <a class="btn btn-success mb-4" href="javascript:void(0)" id="createNewbank"> Create New Bank Detail</a>


        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>BSB Number</th>
                    <th>Account Number</th>
                    <th>Name</th>
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
                <form id="bankForm" name="bankForm" class="form-horizontal" enctype="multipart/form-data">
                @csrf

                   <input type="hidden" name="id" id="id">

                    <div class="form-group">
                        <label for="bsb_no" class="col-sm-6 control-label">BSB Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="bsb_no" name="bsb_no" value="" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="acct_no" class="col-sm-6 control-label">Account Number</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="acct_no" name="acct_no" value="" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="account_name" class="col-sm-6 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="account_name" name="account_name" value="" required="">
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
        ajax: "{{ route('banks.index') }}",

        columns: [
            {data: 'bsb_no', name: 'bsb_no'},
            {data: 'acct_no', name: 'acct_no'},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
        
    });

    $('#createNewbank').click(function () {
        $('#saveBtn').val("create-bank");
        $('#id').val('');

        $('#bankForm').trigger("reset");
        $('#modelHeading').html("Create New Bank Detail");
        $('#ajaxModel').modal('show');

    });

    $('body').on('click', '.editBank', function () {
      var banks = $(this).data('id');
      $.get("{{ route('banks.index') }}" +'/' + banks +'/edit', function (data) {
          $('#modelHeading').html("Edit bank");
          $('#saveBtn').val("edit-bank");
          $('#ajaxModel').modal('show');
          $('#id').val(data.id);
          $('#bsb_no').val(data.bsb_no);
          $('#acct_no').val(data.acct_no);
          $('#account_name').val(data.name);

      })

    });

      
    $('#saveBtn').click(function (e) {
        $('#job_id').attr('disabled', false); 

        var formData = new FormData($('#bankForm')[0]);
        e.preventDefault();

        $(this).html('Saving..');
        $.ajax({
            url: "{{ route('banks.store') }}",
            type: "POST",
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                $('#bankForm').trigger("reset");
                $('#ajaxModel').modal('hide');
                table.draw();
                $('#saveBtn').html('Save Changes');
                toastr.success('Bank Detail saved successfully!');
            },
            error: function (data) {
                $('#saveBtn').html('Save Changes');
                toastr.error('Error Saving!');

            }
        });
    });


    $('body').on('click', '.deleteBank', function () {

        var bank = $(this).data("id");
        var response = confirm("Are You sure want to delete?");

        if (response == true) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('banks.store') }}"+'/'+bank,

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