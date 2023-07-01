@extends('layouts.app')

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="{{ asset('js/sigPad.js') }}"></script>

@section('content')
<div class="d-flex flex-column align-items-center">
    <div class="row">
        <div class="col-lg-12 align-items-center m-2 p-4 d-flex flex-column">
            <h5 class="text-center">TIMECLOCK</h5>
            <h1 id='ct7' class="d-flex justify-content-center p-5" style="font-size: 5rem;"></h1>
            <h3>{{date("Y.m.d")}}</h3>
            <hr/>
            @if((isset($clock_in) && $clock_in->end_time) || !isset($clock_in))
                <button class="btn btn-success btn-lg" id="clock-in"> CLOCK IN </button>
            @elseif(isset($clock_in) && $clock_in->end_time == null)
                <h6>Last Clock In {{date('h:i:s a', strtotime($clock_in->start_time))}}. {{$clock_in->date}}</h6>
                <button class="btn btn-danger btn-lg" id="clock-out"> CLOCK OUT </button>
            @endif
        </div>
    </div>

    @if($job)
    <div class="card" style="width: 95%;">
        <div class="card-header fw-bold">
            Job Details
        </div>
        <div class="row m-2 p-4">

            <div class="col-lg-6">
                <span class='fw-bold'>Job ID:</span>    {{$job->id}}<br/>
                <span class='fw-bold'>Company Name:</span>    {{$job->company_name}}<br/>
                <span class='fw-bold'>Address:</span>    {{$job->address}}<br/>
            </div>
            <div class="col-lg-6">
                <span class='fw-bold'>Job Title:</span>    {{$job->title}}<br/>
                <span class='fw-bold'>Date Duration:</span>    {{$job->start_date_time}} - {{$job->end_date_time}}<br/>
            </div>

        </div>
        @if(isset($clock_in) && $clock_in->end_time == null)
        <form id="timelogForm" name="timelogForm" class="form-horizontal">
        @csrf

            <input type="hidden" name="job_id" id="job_id" value="{{$job->id}}">
            <div class="row m-2 p-2">
            <div class="text-danger">Please fill out form and save before clocking out.</div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="authorized" class="col-sm-6 control-label">Authorized Person</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="authorized" name="authorized" value="{{$clock_in ? $clock_in->authorized : '' }}" >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-6 control-label">Signature</label>
                        <div class="col-sm-12">
                            @if($clock_in->signature)
                            <img src="{{url('/signature/'.$clock_in->signature.'')}}" alt="Image"/>
                            
                            @endif
                            <!-- <div id="signaturePad" ></div> -->
                            
                            <br/>
                            <!-- <button id="clear" class="btn btn-danger btn-sm">Clear Signature</button> -->
                            <textarea id="signature64" name="signed" style="display:none;"></textarea>
                        </div>
                        <div class="col-sm-12">
                            <div class="sigpad-wrapper">
                                <!-- <img id="sigPadImg" src=""/> -->
                                <canvas id="sigpad" class="signaure-pad" width=300 height=180></canvas>
                            </div>
                            <div>
                                <button id="clearPad" class="btn btn-danger mt-2">Clear</button>
                                <button type="submit" class="btn btn-primary mt-2 ml-4" id="saveBtn" value="create">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">

                    <div class="form-group">
                        <label for="lunch_break" class="col-sm-6 control-label">Did you have lunch break?</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="lunch_break" id="lunch_break" required="" value="{{$clock_in ? $clock_in->lunch_break : '' }}" autocomplete="off">
                                <option value="" >-- Select --</option>
                                <option value="0" selected="{{$clock_in->lunch_break == '0' ? 'selected' : '' }}"> No</option>
                                <option value="1" selected="{{$clock_in->lunch_break == '1' ? 'selected' : '' }}"> Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-10 mt-3 pt-5">
                        <!-- <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save</button> -->
                    </div>
                </div>
            </div>
        </form>
        <!-- @include('timelogs.signature-pad') -->
        @endif
    </div>

    @endif
    
</div>


@stop

@section('scripts')
<script type="text/javascript">

    function display_ct7() {
        // var x = new Date().toLocaleString("en-US", {timeZone: "Australia/Sydney"});
        var date = new Date();
        var x = new Date(date.toLocaleString('en-US', {
            timeZone: "Australia/Sydney"
        }));
        var ampm = x.getHours( ) >= 12 ? ' PM' : ' AM';
        hours = x.getHours( ) % 12;
        hours = hours ? hours : 12;
        hours=hours.toString().length==1? 0+hours.toString() : hours;

        var minutes=x.getMinutes().toString()
        minutes=minutes.length==1 ? 0+minutes : minutes;

        var seconds=x.getSeconds().toString()
        seconds=seconds.length==1 ? 0+seconds : seconds;

        var month=(x.getMonth() +1).toString();
        month=month.length==1 ? 0+month : month;

        var dt=x.getDate().toString();
        dt=dt.length==1 ? 0+dt : dt;

        // var x1=month + "/" + dt + "/" + x.getFullYear(); 
        x1 = hours + ":" +  minutes + ":" +  seconds + " " + ampm;
        document.getElementById('ct7').innerHTML = x1;
        display_c7();
    }

    function display_c7(){
        var refresh=1000; // Refresh rate in milli seconds
        mytime=setTimeout('display_ct7()',refresh)
    }
    display_c7();
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#clock-in').click(function (e) {

            var dataToSend = {
                'type': 'in',
                '_token': '{{ csrf_token() }}'
            };

            $.ajax({
                url: "clock_in_out",
                type: "POST",
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

        $('#clock-out').click(function (e) {

            // var dataToSend = {
            //     'type': 'out',
            //     '_token': '{{ csrf_token() }}'
            // };
            var formData = new FormData($('#timelogForm')[0]);
            formData.append('type', 'out');

            $.ajax({
                url: "clock_in_out",
                type: "POST",
                dataType: 'json',
                processData: false,
                contentType: false,
                data: formData,
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

        
    });

    $(document).ready(function(){        

        var sigCanvas = document.querySelector("#sigpad");

        var signaturePad = new SignaturePad(sigCanvas);


        $('#saveBtn').click(function (e) {

            e.preventDefault(); 

            imgdata = signaturePad.toDataURL('image/png');   

            var formData = new FormData($('#timelogForm')[0]);
            formData.append('signed', imgdata);
            
            console.log(imgdata);

            $(this).html('Saving..');
            $.ajax({
                url: "clock_in_out",
                type: "POST",
                dataType: 'json',
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    $('#saveBtn').html('Save');
                    if(data.success) {
                        toastr.success(data.success, 'SUCCESS');
                    } else {
                        toastr.error(data.error, 'ERROR');
                    }
                },
                error: function (data) {
                    $('#saveBtn').html('Save');
                    toastr.error('Error Saving!');

                }
            });

        });

        $('#clearPad').click(function(e) {
            e.preventDefault();
            signaturePad.clear();
        });
    });
    
</script>

@stop


