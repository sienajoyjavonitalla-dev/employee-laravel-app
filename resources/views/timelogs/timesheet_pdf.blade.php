<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');
/* @font-face { 
font-family: Muli-Bold; src: url('/fonts/Muli-Bold.tff');
} */
.flex-right {
    display: flex;
    flex-direction: row-reverse;
}
body {
    margin-right: 50px;
    margin-top: 25px;
    margin-left: 50px;
    font-family: 'Roboto', sans-serif;
    font-size: 10px;
}
.m-0 {
    margin:0;
}

.brand-image {
    width: 50;
    /* float: left; */
}

.weight-normal {
    font-weight:normal;
}

#timesheet {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#timesheet td, #timesheet th {
  border: 1px solid #ddd;
  padding: 8px;
}

#timesheet tr:nth-child(even){background-color: #f2f2f2;}

#timesheet tr:hover {background-color: #ddd;}

#timesheet th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #007bff;
  color: white;
}
.box {
    text-align: right;
    position: relative;
}
.box1 {
	position: absolute;
    left: 0;
}
.notes-box {
    position: absolute;
    left: 0;
    width: 200px;
    text-align: left;
}
.box1 h4 {
    text-align: left;
}

</style>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Pdf Download</title>
  </head>
  <body>
    <div class='box'>
        <div class='box1'>
            <h1 style="text-align: left;">TIMESHEET</h1>

            <h3 class='weight-normal' style="text-align: left;">
                Site Location: {{$dataArr['first']->address ?? ''}}<br/>
                Week Starting: {{$dataArr['first']->date ?? ''}}<br/>
            </h3>
        </div>
        <div class='box2'>
        </div>
        <div class='box3'>
            <h5 class='flex-right weight-normal'>
                <img src="{{url('/uprise_rigging.png')}}" alt="Image">
            </h5>
        </div>
    </div>

    <br/>
    <span>
        <table id="timesheet">
            <tr>
                <th>Operator</th>
                <th style="width: 15%;">Date</th>
                <th>Day</th>
                <th>Start</th>
                <th>Finish</th>
                <th style="width: 5%;">Lunch</th>
                <th>Supervisor</th>
                <th style="width: 30%;">Signature</th>
            </tr>
            @php
                $subtotal = 0;
            @endphp

            @foreach ($dataArr['data'] as $data)
                @php
                    $day = Carbon\Carbon::parse($data->date)->format('l')

                @endphp
            <tr>
                <td>{{$data->name}}</td>
                <td>{{ $data->date }}</td>
                <td>{{ $day }}</td>
                <td>{{ $data->start_time }}</td>
                <td>{{ $data->end_time }}</td>
                <td>{{ $data->lunch_break ? 'Yes' : 'No' }}</td>
                <td>{{ $data->authorized }}</td>
                <td><img style="width: 30%;" src="{{$data->signature ? url('/signature/'.$data->signature.'') : ''}}" alt="Image"></td>
            </tr>
            @endforeach
        </table>
        <!-- @php
            $signature = '';
            $authorized = '';
            foreach ($dataArr['data'] as $data) {
                if($data->signature && $data->authorized) {
                    $signature = $data->signature;
                    $authorized = $data->authorized;
                    break;
                }
            }
        
        @endphp
        <br/><br/>
        <h3>Client Signoff</h3>
        <h5>Site Supervisor Name: {{$authorized}}<br/>
        Signature:</h5> <img src="{{$signature ? url('/signature/'.$signature.'') : ''}}" alt="Image"> -->

        <br/>
    </span> 
  </body>
</html>