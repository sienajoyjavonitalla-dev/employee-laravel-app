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
                            <label for="client_name" class="col-sm-6 control-label">Employee</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-employee-name" name="event-employee-name" value="" maxlength="50" disabled="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="col-sm-6 control-label">Status</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-status" name="event-status" value="" maxlength="50" disabled="true">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="rate_per_hour" class="col-sm-6 control-label">Comment</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-comment" name="event-comment" value="" disabled="true">
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

                        let htmlString = "<b>#" + info.event.extendedProps.job_id + " " + info.event.title + "</b><br>" +
                                        "<i>" + info.event.extendedProps.client + "</i> <br>" +
                                        info.event.extendedProps.employee + "<br>";

                        return {html : htmlString};
                    },
                    eventClick : function(info){

                        let eventTitle = document.getElementById("event-title");
                        let eventClientName = document.getElementById("event-client-name");
                        let eventEmployeeName = document.getElementById("event-employee-name");
                        let eventStatus = document.getElementById("event-status");
                        let eventComment = document.getElementById("event-comment");

                        eventTitle.value = "#" + info.event.extendedProps.job_id + " " + info.event.title;
                        eventClientName.value = info.event.extendedProps.client;
                        eventEmployeeName.value = info.event.extendedProps.employee;
                        eventStatus.value = info.event.extendedProps.status;
                        eventComment.value = info.event.extendedProps.comment;

                        $('#ajaxModel').modal('show');
                    }
                });

                calendar.render();
            });

        </script>

    @endpush

@stop
