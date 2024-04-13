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
    margin-right: 10px;
    margin-top: 15px;
    margin-left: 10px;
    font-family: 'Roboto', sans-serif;
    font-size: 10px;
}
table {
    font-size:10px;
}
td {
    height: 10px;
    padding:3px !important;
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

.weight-bold {
    font-weight:bold;
    font-size: large;
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

.container {
    display: flex;
    flex-wrap: wrap;
}

.column {
    flex: 1;
    /* padding: 20px; */
    margin: 10px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Vertically center content */
}

.input_text {
    height: 15px;
    width: 75% !important;
}

.column input[type="text"] {
    width: fit-content;
    padding: 8px;
    box-sizing: border-box;
    margin-bottom: 10px;
}

.align-inputs {
    width: 20%;
}

table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: left;
}
/* Print-specific styles */
@media print {
        table {
            display: block;
            page-break-inside: auto;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        tr {
            page-break-inside: avoid;
            display: table-row;
        }
        th, td {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
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
    @php
        $operator = "";
        $dogmen = "";
        $day = Carbon\Carbon::parse($dataArr['first']->date)->format('l');

        $total_hr = 0;
        $end_time_str = $dataArr['first']->end_time;

        $start_time = new DateTime($dataArr['first']->start_time);
        $end_time = new DateTime($dataArr['first']->end_time);

        // Calculate the difference between the start and end times
        $difference = $start_time->diff($end_time);

        // Get the total number of hours from the difference
        $total_hr = $difference->h + ($difference->days * 24);

        if($total_hr < 4) {
            $add_four = date("H:i:s", strtotime($dataArr['first']->start_time) + 4 * 3600);
            $end_time_str = add_four;
        }

        // Subtract 30 minutes from the start time
        $depart_depot = $start_time->modify('-30 minutes');
        $arrive_depot = $end_time->modify('+30 minutes');

        // Get the modified time as a string
        $depart_depot = $start_time->format('H:i:s');
        $arrive_depot = $end_time->format('H:i:s');

        // Create a DateTime object from the military time string
        $start_datetime = DateTime::createFromFormat('H:i:s', $dataArr['first']->start_time);
        $end_datetime = DateTime::createFromFormat('H:i:s', $end_time_str);
        $depart_depot_datetime = DateTime::createFromFormat('H:i:s', $depart_depot);
        $arrive_depot_datetime = DateTime::createFromFormat('H:i:s', $arrive_depot);

        // Format the DateTime object into the desired format (12-hour with AM/PM)
        $start_standard_time_str = $start_datetime->format('h:i:s A');
        $end_standard_time_str = $end_datetime->format('h:i:s A');
        $depart_depot_standard_time_str = $depart_depot_datetime->format('h:i:s A');
        $arrive_depot_standard_time_str = $arrive_depot_datetime->format('h:i:s A');

        // Extract the AM/PM indicator from the time string
        $start_am_pm = substr($start_standard_time_str, -2);
        $end_am_pm = substr($end_standard_time_str, -2);
        $depart_depot_am_pm = substr($depart_depot_standard_time_str, -2);
        $arrive_depot_am_pm = substr($arrive_depot_standard_time_str, -2);
        
        

    @endphp  
    @foreach ($dataArr['data'] as $data)

        @if($data->job_title == "operator")
            @php
                $operator .= $data->name . ",";
            @endphp
        @else
            @php
                $dogmen .= $data->name . ",";
            @endphp

        @endif
        
    @endforeach
    <div class="container">
        <div class="column">
            <h5 class='weight-normal'>
                <img style="width:50%;" src="{{url('/docket_logo.png')}}" alt="Image">
            </h5>
        </div>
        <div class="column">
            <h4>ABN: {{$dataArr['first']->abn ?? ''}} </br>
                Email: admin@upriserigging.com</br>
            </h4>
            <h4>
                0426 964 330</br>
                0414 376 589
            </h4>
        </div>
        <div class="column">
            <h4>
                Docket No. {{$dataArr['first']->id ?? ''}}
                <span style="display: flex;">
                    Invoice No.
                </span>
                <span style="display: flex;">
                    Day: 
                    {{$day}}
                </span>
                <span style="display: flex;">
                    Date: 
                    {{ $dataArr['first']->date }}
                </span>
            </h4>
        </div>
    </div>
    <span style="display: flex;">Client: {{$dataArr['first']->company_name ?? ''}}</span>
    <span style="display: flex;">Site Location: {{$dataArr['first']->address ?? ''}}</span>
    <span style="display: flex;">Job Description: {{$dataArr['first']->description ?? ''}}</span>

    <br/>
    <table style="width: 100%;">
        <tr>
            <th style="width: 50%;">Operator: {{$operator}} </th>
            <th>Dogmen: {{$dogmen}} </th>
        </tr>
    </table>

    <div class="container">
        <div class="column">
            <span>Spotter<input type="checkbox"></span>
        </div>
        <div class="column">
            <span>Permits<input type="checkbox"></span>

        </div>
        <div class="column">
            <span>Delivery<input type="checkbox"></span>
        </div>
        <div class="column">
            <span>C.O.D.<input type="checkbox"></span>
        </div>
        <div class="column">
            <span>Account<input type="checkbox"></span>
        </div>
    </div>
    <span>Other: <input style="width:50%" type="text"/></span>

    <div class="container">
        <div class="column">
            <h4>PRE JOB INSPECTION CHECK LIST</h4>
            <span>Complete prior to commencing job</span>
            <table>
                <tr>
                    <th></th>
                    <th>YES</th>
                    <th>NO</th>
                </tr>
                <tr>
                    <td>Does the site comply with the “NO GO ZONE” rules?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is a spotter required?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is site access for plant satisfactory?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is site access for plant satisfactory?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Are ground conditions suitable for safe operation?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is the job within the capacity of the crane?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Are weather conditions suitable for safe operaton?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is work area access for personnel satisfactory?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Have all relevant permits been issued?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is fall protection required and supplied?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Is traffic control required?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td>Lifting Gear Tagged & Tested?</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox"></td>
                </tr>
                <!-- Add more rows as needed -->
            </table>
        </div>
        <div class="column">
            <h4>TIMES TO THE NEAREST FORWARD HALF HOUR</h4></br>
            <table>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>

                </tr>
                <tr>
                    <td>Depart Depot</td>
                    <td> {{$depart_depot}}</td>
                    <td><input type="checkbox" {{ $depart_depot_am_pm ? '' : 'checked' }}>AM</td>
                    <td><input type="checkbox" {{ $depart_depot_am_pm ? 'checked' : '' }}>PM</td>
                </tr>
                <tr>
                    <td>Arrive Site</td>
                    <td>{{$start_standard_time_str}}</td>
                    <td><input type="checkbox" {{ $start_am_pm ? '' : 'checked' }}>AM</td>
                    <td><input type="checkbox" {{ $start_am_pm ? 'checked' : '' }}>PM</td>
                </tr>
                <tr>
                    <td>Meal</td>
                    <td>{{ $dataArr['first']->lunch_break ? 'Yes' : 'No'}}</td>
                    <td><input type="checkbox" {{ $dataArr['first']->lunch_break ? 'checked' : ''}} >Yes</td>
                    <td><input type="checkbox" {{ $dataArr['first']->lunch_break ? '' : 'checked'}}>No</td>
                </tr>
                <tr>
                    <td>Depart Site</td>
                    <td>{{$end_standard_time_str}}</td>
                    <td><input type="checkbox" {{ $end_am_pm ? '' : 'checked' }}>AM</td>
                    <td><input type="checkbox" {{ $end_am_pm ? 'checked' : '' }}>PM</td>
                </tr>
                <tr>
                    <td>Arrive Depot</td>
                    <td>{{$arrive_depot_standard_time_str}}</td>
                    <td><input type="checkbox" {{ $arrive_depot_am_pm ? '' : 'checked' }}>AM</td>
                    <td><input type="checkbox" {{ $arrive_depot_am_pm ? 'checked' : '' }}>PM</td>
                </tr>
                <!-- Add more rows as needed -->
            </table>
            <!-- <h4>OFFICE USE ONLY</h4>
            <table>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>

                </tr>
                <tr>
                    <td>Crane</td>
                    <td style="width:10%">hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Op Travel</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Rigger/Dogman</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Spotter/Traffic</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Welder/Labour</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Penalties</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Counterweight</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Meals</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Transport</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Permit</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Toll/Fuel Levy</td>
                    <td>hrs @</td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td>Equipment</td>
                    <td></td>
                    <td><input class="input_text" type="text"/></td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Subtotal</td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>+ GST</td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    <td><input class="input_text" type="text"/></td>
                </tr>
            </table> -->
        </div>
    </div>
    </br>
    </br>
    </br>
    <table>
        <tr>
            <td colspan="2">
                <h3 style="text-align: center;">We acknowledge that all work is performed subject to the terms and conditions printed on the back hereof. </br>
                    Work completed to our satisfaction and times are correct.
                </h3>
            </td>
        </tr>
        <tr>
        <td>
                For and on behalf of the Client: </br></br>
                Print Name: {{$dataArr['first']->authorized}} </br>
                <img style="width: 20%;" src="{{$dataArr['first']->signature ? url('/signature/'.$data->signature.'') : ''}}" alt="Image">

        </td> 
        <td>
                For and on behalf of Uprise Rigging Pty Ltd: </br></br>
                Operator / Dogmen Names: </br>
                @foreach ($dataArr['data'] as $data)
                    @php
                    // Split the string by space
                    $name = explode(" ", $data->name);

                    // Get the first word
                    $first_name = $name[0];
                    
                    @endphp
                    Name: {{$first_name}}</br>
                @endforeach
        </td>
        </tr>
    </table>
    </br>
    </br>
    </br>
    </br>

    <h1 style="page-break-before: always;">TERMS AND CONDITIONS</h1>

    <p><strong>1. General</strong><br>
    The whole of the Agreement between Uprise Rigging Pty Ltd 79 647 093 310 and the Applicant referred to in the Credit Application (“Customer”) are those set out in these
    Terms and Conditions as amended from time to time and those, if any, which are implied and which cannot be excluded by law (“Terms”). Any other contractual terms of the
    Customer (whether upon the Customer’s order or elsewhere) which are contrary to or inconsistent with these Terms shall not apply nor shall they constitute a counter-offer.
    By receiving delivery and/or supply of all or a portion of the goods, materials and/or parts and/or labour and/or services supplied by Uprise Rigging under these Terms.</p>

    <p><strong>3. Quotations and Pricing</strong><br>
    Prices charged for Services will be according to a current quotation for those Services. Otherwise, they will be determined by Uprise Rigging
    by reference to its standard prices in effect at the date of delivery (whether notified to the Customer or not and regardless of any prices
    contained in the order). Uprise Rigging will use its best endeavours to notify the Customer of price changes but bears no liability in respect of
    this.</p>

    <p><strong>4. Delivery & Supply</strong><br>
    Any times quoted for delivery and/or supply are estimates only and Uprise Rigging shall not be liable for failure to deliver/supply, or for delay
    in delivery/supply. The Customer shall not be relieved of any obligation to accept or pay for Services, by reason of any delay in delivery/
    supply or dispatch. Uprise Rigging reserves the right to stop supply at any time if the Customer fails to comply with the Terms.</p>

    <p><strong>5. Conditions of Supply of Services</strong><br>
    The following conditions will apply in regard to the hire of equipment and supply of personnel to operate the equipment:<br>
    5.1 The Customer will indemnify Uprise Rigging from all claims arising out of accidents either to persons or to vehicles or to other property caused by the equipment
    used on the site.<br>
    5.2 The Customer shall be responsible for giving any local or other authorities any necessary notice of intention to erect or use the equipment and shall pay all fees or
    any fines with respect to such notification or any failure to notify.<br>
    5.3 The Customer shall ensure that the site is safe and clear for use of the equipment (eg overhead obstructions) and shall notify Uprise Rigging of any possible
    problems to ensure the correct equipment is dispatched. If the Customer does not notify Uprise Rigging and incorrect equipment is dispatched, this will be deemed a
    cancellation.<br>
    5.4 The Customer shall provide all necessary barricades, protective awnings, traffic management and signs that may be required under any permits or regulations<br>
    5.5 Uprise Rigging shall at all times have the right to inspect the site or any of the equipment on hire.<br>
    5.6 The Customer shall supply hard-standing access for the equipment set up and shall be responsible for the tow or removal of equipment back to hard-standing.<br>
    5.7 The Customer shall ensure the site is safe and all necessary precautions for the safety of the operators of the equipment are undertaken in
    accordance with occupational health and safety requirements and regulations, and shall indemnify Uprise Rigging from any claim arising out of
    the Customer’s failure to comply with this clause.</p>

    <p><strong>6. Warranties</strong><br>
    6.1 No warranties except those implied and that by law cannot be excluded are given by Uprise Rigging in respect of Services supplied. Where it is lawful to do so, the
    liability of Uprise Rigging for a breach of a condition or warranty is limited to the supply of equivalent Services, or the
    cost of acquiring equivalent Services, as determined by Uprise Rigging.<br>
    6.2 The Customer warrants to Uprise Rigging that it is purchasing the Services as the principal and not as an agent.</p>

    <p><strong>7. Force Majeure</strong><br>
    Uprise Rigging shall be released from its obligations in the event of national emergency, war, prohibitive governmental regulation or if any other cause beyond the control
    of the parties renders provision of the Services impossible, where all money due to Uprise Rigging shall be paid immediately and, unless prohibited by law, Uprise
    Rigging may elect to terminate the Agreement.</p>

    <p><strong>8. Legal Construction</strong><br>
    8.1 These Terms shall be governed by and interpreted according to the laws of Victoria and Uprise Rigging and the Customer consent and submit
    to the jurisdiction of the Courts of Victoria.<br>
    8.2 Notwithstanding that any provision of the Terms may prove to be illegal or unenforceable pursuant to any statute or rule of law or for any other reason that provision
    is deemed omitted without affecting the legality of the remaining provisions and the remaining provisions of the Terms shall continue in full force and effect.
    (“Services”), the Customer shall be deemed to have accepted these Terms and to have agreed that they shall apply to the exclusion of all others.<br>
    a rate of 15% per annum 2. Credit Terms<br>
    2.1 Payment is due on completion of works unless a prior trading account has been approved in writing by Uprise Rigging Accounts Department. Uprise Rigging reserves
    the right to place your account on credit hold should our agreed trading terms not be adhered too. Uprise Rigging may charge interest at a rate of 15% per annum if
    payment in not received by the due date.<br>
    2.2 Should you require a purchase order numbers on your invoices, we are happy to accommodate please ensure your purchase order number is quoted at the time of
    Booking your job with us, we take no responsibility for collecting these and it is an unacceptable reason for payment to be delayed as purchase order numbers are not
    required by Uprise Rigging Pty Ltd.<br>
    2.3 The Customer is liable for all reasonable expenses (including contingent expenses such as debt collection commission) and legal costs
    (on a full indemnity basis) incurred by Uprise Rigging for enforcement of obligations and recovery of monies due from the Customer to Uprise Rigging.</p>

  </body>
</html>