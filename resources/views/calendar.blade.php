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
                    <p id="event-title"></p>
                    <p id="event-client-name"></p>
                    <p id="event-employee-name"></p>
                    <p id="event-status"></p>
                    <p id="event-comment"></p>
                    <hr>
                    <p id="event-invoice">Invoice Link</p>

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

                        let htmlString = "<b>" + info.event.title + "</b><br>" +
                                        "<i>" + info.event.extendedProps.client + "</i> <br>" +
                                        "Assigned: " + info.event.extendedProps.employee + "<br>";

                        return {html : htmlString};
                    },
                    eventClick : function(info){

                        let eventTitle = document.getElementById("event-title");
                        let eventClientName = document.getElementById("event-client-name");
                        let eventEmployeeName = document.getElementById("event-employee-name");
                        let eventStatus = document.getElementById("event-status");
                        let eventComment = document.getElementById("event-comment");

                        eventTitle.innerHTML = info.event.title;
                        eventClientName.innerHTML = info.event.extendedProps.client;
                        eventEmployeeName.innerHTML = info.event.extendedProps.employee;
                        eventStatus.innerHTML = info.event.extendedProps.status;
                        eventComment.innerHTML = info.event.extendedProps.comment;

                        $('#ajaxModel').modal('show');
                    }
                });

                calendar.render();
            });

        </script>

    @endpush

@stop
