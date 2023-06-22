@extends('layouts.app')

@section('content')

    <div id="calendar"></div>

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
                            <label for="client_name" class="col-sm-6 control-label">Job Title</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-title" name="event-title" value="" maxlength="50" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="abn" class="col-sm-6 control-label">Client</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-client-name" name="event-client-name" value="" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="client_name" class="col-sm-6 control-label">Employee(s)</label>
                            <div class="col-sm-12">
                                <textarea class="form-control" id="event-employee-name" name="event-employee-name" value="" rows="3" disabled="true">
                                </textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="col-sm-6 control-label">Status</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-status" name="event-status" value="" maxlength="50" disabled="true">
                            </div>
                        </div>
                    </form>

                    <hr>
                    <p class="invoice-link"><a href="#">INVOICE LINK</a></p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js'></script>

        <script> 

            document.addEventListener('DOMContentLoaded', function () {

                var calendarEl = document.getElementById('calendar');
                var events = @json($events);

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: events,
                    height: "auto",
                    eventContent : function(info){

                        data = info.event.extendedProps;
                        assignee = "Assigned: N/A";

                        if(data.employee.length > 0)
                        {         
                            assignee = "Assigned:<br>";

                            data.employee.forEach(function(item){
                                assignee += item.name + "<br>";
                            });
                        }

                        if(data.status == "open") assignee = "<b>JOB IS OPEN</b>";

                        htmlString =    "<b>#" + data.job_id + " " + info.event.title + " </b>" +
                                        "<i>" + data.client + "</i> <br>" + assignee;

                        return {html : htmlString};
                    },
                    eventClick : function(info){

                        data = info.event.extendedProps;

                        eventTitle = document.getElementById("event-title");
                        eventClientName = document.getElementById("event-client-name");
                        eventEmployeeName = document.getElementById("event-employee-name");
                        eventStatus = document.getElementById("event-status");
                        eventComment = document.getElementById("event-comment")

                        eventTitle.value = "#" + data.job_id + " " + info.event.title;
                        eventClientName.value = data.client;
                        eventStatus.value = data.status;

                        console.log(data.employee);

                        employees = "";

                        if(data.employee.length > 0)
                        {
                            data.employee.forEach(function(item){
                                employees +=    item.name + " (" + item.jobTitle + ")\n";
                            });
                        } 
                        else
                        {

                        }
                        eventEmployeeName.value = employees;

                        $('#ajaxModel').modal('show');
                    }
                });

                calendar.render();
            });

        </script>

    @endpush

@stop
