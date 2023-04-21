@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 align-items-center border m-2 rounded-4 p-4 d-flex flex-column">
        <h5 class="text-center">TIMECLOCK</h5>
        <h1 id='ct7' class="d-flex justify-content-center p-5" style="font-size: 5rem;"></h1>
        <h3>{{date("Y.m.d")}}</h3>
        @if((isset($clock_in) && $clock_in->end_time) || !isset($clock_in))
            <button class="btn btn-success btn-lg"> CLOCK IN </button>
        @elseif(isset($clock_in) && $clock_in->end_time == null)
            <button class="btn btn-danger btn-lg"> CLOCK OUT </button>
        @endif
    </div>
</div>

@stop

@section('scripts')
<script type="text/javascript">

    function display_ct7() {
        var x = new Date()
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
</script>

@stop


