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
    <img src="uprise_rigging.png" alt="uprise Logo" class="brand-image img-circle">
</div>
    <div class='box2'>
</div>
    <div class='box3'>
    <span class='flex-right'><h3 class='m-0'>Uprise Rigging Pty Ltd</h3></span>
    <h5 class='flex-right weight-normal'>
        Yan Yean Rd, Doreen VIC3754<br/>
        Phone: 0426964330<br/>
        admin@upriserigging.com<br/>
        www.upriserigging.com<br/>
        ABN: 79 647 093 310<br/>
    </h5>
    </div>
</div>
    <div>
        <h4>Tax Invoice</h4>
        <table id="invoice">
            <tr>
                <th>Purchase Order #</th>
                <th>Invoice #</th>
                <th>Issue Date</th>
                <th>Due Date</th>
            </tr>
            <tr>
                <td>{{$dataArr['first']->po_number}}</td>
                <td></td>
                <td>{{ date("Y-m-d") }}</td>
                <td>{{ date("Y-m-d") }}</td>
            </tr>
        </table>
    </div>
    <h4>
        Bill to
    </h4>
    <span>
        {{$dataArr['first']->client_name}} <br/>
        {{$dataArr['first']->company_name}} <br/>
        {{$dataArr['first']->address}} <br/>
    </span>
    <br/>
    <span>
        <table id="invoice">
            <tr>
                <th>Item ID</th>
                <th>Description</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Unit Price ($)<br/>Excluding Tax</th>
                <th>Tax</th>
                <th>Amount ($)<br/>Excluding Tax</th>
            </tr>
            @php
                $subtotal = 0;
            @endphp

            @foreach ($dataArr['data'] as $index=>$data)
                @php
                    $total_hr = 0;
                    $start_time = new Carbon\Carbon($data->start_time);
                    $end_time =new Carbon\Carbon($data->end_time);
                    $total_hr = $start_time->diffInHours($end_time);
                        if($total_hr > 4) {
                            $ot_pay=0;
                            $pay = $total_hr * $data->rate_per_hour;
                            if($total_hr > 8) {
                                $ot_hours= $total_hr - 8;
                                $ot_pay = $ot_hours * $data->ot_rate_per_hour;
                            }
                            $total_amount = $ot_pay + $pay;
                        } else if($total_hr <= 4) {
                            $pay = 4 * $data->rate_per_hour;
                            $total_amount = $pay;
                        }
                        $subtotal += $total_amount;
                @endphp
            <tr>
                <td>Service</td>
                <td>{{ $data->date . ' ' . $data->name }}</td>
                <td>Hour</td>
                <td>{{ $total_hr }}</td>
                <td>{{ $data->rate_per_hour }}</td>
                <td>GST</td>
                <td>{{ $total_amount }}</td>

            </tr>
            @endforeach
            <tr>
                <td>Travel Allowance</td>
                <td>Travel Allowance</td>
                <td>Per Day</td>
                <td>{{$dataArr['data']->count()}}</td>
                <td>{{$dataArr['first']->travel_allowance}}</td>
                <td>GST</td>
                <td>{{ $dataArr['data']->count() * $dataArr['first']->travel_allowance }}</td>
            </tr>
        </table>
        @php
            $sub_ta = $subtotal + ($dataArr['data']->count() * $dataArr['first']->travel_allowance);
            $tax = $sub_ta *.1;
        @endphp   
        <span class="box" >         
            <div><span class='title'>Subtotal</span> <span>{{$sub_ta}}</span></div> 
            <div><span class='title'>Tax</span> <span>{{$tax}}</span></div>
            <div><span class='title'>Total Amount</span> <span>{{$sub_ta + $tax}}</span></div>
            <div><span class='title'>Balance Due</span> <span>{{$sub_ta+ $tax}}</span></div>
        </span>
    </span> 
  </body>
</html>