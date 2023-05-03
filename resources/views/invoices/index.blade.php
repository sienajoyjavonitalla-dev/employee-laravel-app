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
                <strong>PO# </strong>
                <input id="po_number" type="text" value="" />
            </div>
            
        </div>
        <div class="row pt-4">
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
                <strong>Assigned </strong>
                <select  name="assigned" id="assigned">
                    <option value="">-- Select --</option>
                    @foreach ($assigned as $key => $value)
                        <option value="{{ $key }}"> 
                            {{ $value }} 
                        </option>
                    @endforeach    

                </select>
            </div>
        </div>
        <button class="w-auto mb-4 mt-4 btn btn-success filter">Filter</button>
        
        <table class="table table-bordered table-hover data-table">
            <thead class="thead-light">
                <tr>
                    <th>Job</th>
                    <th>PO#</th>
                    <th>Employee</th>
                    <th>Client</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Hours Worked</th>
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

    function load_data(from_date, to_date, client, po_number, assigned) {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {

                url: "{{ route('invoices.index') }}",
                data:{
                    from_date: from_date, 
                    to_date: to_date,
                    client: client,
                    po_number:po_number,
                    assigned: assigned
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'po_number', name: 'po_number'},
                {data: 'employee', name: 'employee'},
                {data: 'client_name', name: 'client_name'},
                {data: 'title', name: 'title'},
                {data: 'date', name: 'date'},
                {data: 'hrs_worked', name: 'hrs_worked'},
                {data: 'pay', name: 'pay'},
                {data: 'ot_pay', name: 'ot_pay'},
                {data: 'total', name: 'total'},

            ]
        });
    }
    

    $(".filter").click(function(){
        var from_date = ($('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD'));
        var to_date = ($('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD'));
        var client = $('#client').val();
        var po_number = $('#po_number').val();
        var assigned = $('#assigned').val();

        if(from_date != '' &&  to_date != '')
        {
            $('.data-table').DataTable().destroy();
            load_data(from_date, to_date, client, po_number, assigned);
        }
        else
        {
            toastr.error('Both Date is required!');
        }
    });
  });

</script>
@stop