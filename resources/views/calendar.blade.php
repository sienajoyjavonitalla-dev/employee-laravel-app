@extends('layouts.app')

@section('content')

    <div id="calendar"></div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js'></script>

        <script> 

            document.addEventListener('DOMContentLoaded', function () {

                var calendarEl = document.getElementById('calendar');
                var events = @json($events);screenTop

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: events,
                    height: "auto",
                    eventContent : function(info){

                        let htmlString = "<b>" + info.event.title + "</b><br>" +
                                        info.event.extendedProps.client + "<br>" +
                                        "Assigned to: " + info.event.extendedProps.employee + "<br>";

                        return {html : htmlString};
                    }
                });

                calendar.render();
            });

        </script>

    @endpush

@stop
