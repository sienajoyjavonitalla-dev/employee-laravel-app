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

#invoice {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#invoice td, #invoice th {
  border: 1px solid #ddd;
  padding: 8px;
}

#invoice tr:nth-child(even){background-color: #f2f2f2;}

#invoice tr:hover {background-color: #ddd;}

#invoice th {
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
    <h3 style="text-align: center;">{{ $dataArr['user']['name']}}</h3>
    <div class='box'>
        <div class='box1'>
            <h5 class='weight-normal' style="text-align: left;">
                Address: {{ $dataArr['user']['address']}}<br/>
                ABN:{{ $dataArr['user']['abn']}}<br/>
            </h5>
        </div>
        <div class='box2'>
        </div>
        <div class='box3'>
            <h5 class='flex-right weight-normal'>
                Phone: {{ $dataArr['user']['contact_no']}}<br/>
                Email: {{ $dataArr['user']['email']}}<br/>
            </h5>
        </div>
    </div>
    <h3 style="text-align: center;">Invoice</h3>
    <div class='box'>
        <div class='box1'>
            <h5 class='weight-normal' style="text-align: left;">
                Invoice #: {{ $dataArr['invoice'] }}
            </h5>
        </div>
        <div class='box2'>
        </div>
        <div class='box3'>
            <h5 class='flex-right weight-normal'>
                Issue Date: {{ date("Y-m-d") }}
            </h5>
        </div>
    </div>

    <div class='box'>
        <div class='box1'>
            <h5 class='weight-normal' style="text-align: left;">
                Uprise Rigging Pty Ltd<br/>
                www.upriserigging.com<br/>
                Yan Yean Rd, Doreen VIC3754<br/>
                ABN: 79 647 093 310<br/>
            </h5>
        </div>
        <div class='box2'>
        </div>
        <div class='box3'>
            <h5 class='flex-right weight-normal' style="padding-top: 1rem;">
                Phone: 0426964330<br/>
                admin@upriserigging.com<br/>
            </h5>
        </div>
    </div>
    
    <br/>
    <span>
        <table id="invoice">
            <tr>
                <th>Item ID</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>OT Hrs</th>
                <th>OT Rate</th>
                <th>Tax</th>
                <th>Amount ($)<br/>Excluding Tax</th>
            </tr>
            @php
                $subtotal = 0;
            @endphp

            @foreach ($dataArr['data'] as $data)
                @php
                    $total_hr = 0;
                    $ot_hours =0;
                    $start_time = new Carbon\Carbon($data->start_time);
                    $end_time =new Carbon\Carbon($data->end_time);
                    $total_mins = $start_time->diffInMinutes($end_time);
                    $total_hr = round($total_mins / 60, 2);
                        if($data->lunch_break) {
                            $total_hr = $total_hr - .5;
                        }

                        $rate = $data->rate_per_hour;
                        $isWeekend = false;
                        if($data->date) {
                            $day = Carbon\Carbon::createFromFormat('Y-m-d', $data->date );
                            $isWeekend = $day->isWeekend();
                            if($isWeekend) {
                                $rate = $data->ot_rate_per_hour;
                            }
                        } 

                        if($total_hr > 4) {
                            $ot_pay=0;
                            $pay = $total_hr * $rate;
                            if($total_hr > 8) {
                                $ot_hours= $total_hr - 8;
                                $ot_pay = $ot_hours * $data->ot_rate_per_hour;
                                $total_hr = 8;
                                $pay = $total_hr * $rate;

                            }
                            $total_amount = $ot_pay + $pay;
                        } else if($total_hr <= 4) {
                            $pay = 4 * $rate;
                            $total_amount = $pay;
                        }
                        
                        $subtotal += $total_amount;
                @endphp
            <tr>
                <td>{{$data->job_title}}</td>
                <td>{{ $data->date . ' ' . $data->name }}</td>
                <td>Hour</td>
                <td>{{ $total_hr }}</td>
                <td>{{ $rate }}</td>
                <td>{{ $ot_hours }}</td>
                <td>{{ $data->ot_rate_per_hour }}</td>
                <td>GST</td>
                <td>{{ $total_amount }}</td>

            </tr>
            @endforeach
            <tr>
                <td>Travel Allowance</td>
                <td>Travel Allowance</td>
                <td>Per Day</td>
                <td>{{$dataArr['data']->count()}}</td>
                <td>50</td>
                <td></td>
                <td></td>
                <td>GST</td>
                <td>{{ $dataArr['data']->count() * 50 }}</td>
            </tr>
        </table>
        @php
            $sub_ta = $subtotal + ($dataArr['data']->count() * 50);
            $tax = $sub_ta *.1;
        @endphp   
        <div class="box" > 
            <div class='notes-box'>
                <div><h4 class='title'>Notes: </h4><span> {{$dataArr['first']->address ?? ''}}</span></div> 
            </div>
            <div class='box3'>        
                <div><span class='title'>Subtotal</span> <span>{{$sub_ta}}</span></div> 
                <div><span class='title'>Tax</span> <span>{{$tax}}</span></div>
                <div><span class='title'>Total Amount Due</span> <span>{{$sub_ta + $tax}}</span></div>
            </div>
        </div>
        <br/><br/>
        Please remit to:<br/>
        BSB Number: {{$dataArr['bank']['bsb_no']}}<br/>
        Account Number: {{$dataArr['bank']['acct_no']}}<br/>
        Reference: {{$dataArr['invoice']}}<br/>
        Total Due: {{$sub_ta+ $tax}}

    </span> 
  </body>
</html>