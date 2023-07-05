@extends('layouts.app')

@section('content')

    <div id="calendar"></div>

    <div class="modal fade" id="ajaxNoteModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="clientForm" name="clientForm" class="form-horizontal p-2">
                    @csrf
                    <div class="form-group">
                        <label for="calendar_note" class="col-sm-12 control-label text-center"><h2>Calendar Note</h2></label>
                        <div class="col-sm-12">
                            <textarea class="form-control" id="calendar_note" name="calendar_note" rows="6" placeholder="Type notes to save"></textarea>
                        </div>
                    </div>

                    <div class="row ml-1">
                        <div class="col-sm-6 mt-4 mb-1">
                            <span id="calendar-note-dete" class="btn btn-danger calendar-note-btn btn-block float-left">Delete Note</span>
                        </div>
                        <div class="col-sm-6 mt-4 mb-1">
                            <span id="calendar-note-save" class="btn btn-info calendar-note-btn btn-block float-right">Save Note</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>

                <div class="modal-body">

                    <form id="clientForm" name="clientForm" class="form-horizontal">
                    @csrf

                        <div class="form-group">
                            <label for="event-po-number" class="col-sm-6 control-label">Client</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-client-name" name="event-client-name" value="" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="event-po-number" class="col-sm-6 control-label">PO Number</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-po-number" name="event-po-number" value="" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="event-description" class="col-sm-6 control-label">Description</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-description" name="event-description" value="" disabled="true">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="client_name" class="col-sm-6 control-label">Assigned To</label>
                            <div class="col-sm-12">
                                <textarea class="form-control" id="event-employee-name" name="event-employee-name" value="" rows="3" disabled="true">
                                </textarea>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="row pl-4 pr-4">
                        <div class="col-sm-6">
                            <p class="invoice-link btn btn-info float-left" id="invoice-link-section">
                                <a href="#" id="invoice-link" target="_blank">
                                    INVOICE LINK
                                </a>
                            </p>
                        </div>

                        @if(Auth::user()->roles == 'admin')
                        <div class="col-sm-6">
                            <p class="invoice-link btn btn-danger float-right">
                                <a href="#" id="job-delete">
                                    Delete
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.7/index.global.min.js'></script>

        <script> 

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            document.addEventListener('DOMContentLoaded', function () {

                var calendarEl = document.getElementById('calendar');
                var events = @json($events);

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridFourWeek',
                    views: {
                        dayGridFourWeek: {
                        type: 'dayGrid',
                        duration: { weeks: 2}
                        }
                    },
                    // initialView: 'dayGridMonth',
                    events: events,
                    height: "90vh",
                    eventContent : function(info){

                        data = info.event.extendedProps;

                        var htmlString = "";

                        if(data.eventType == 'job')
                        {
                            status = "ASSIGNED";
                            htmlString =    "<b>#" + data.job_id + "</b> " + " - <i>" + data.status.toUpperCase() + "</i><br>" +
                                            data.client;

                        }
                        else if(data.eventType == 'note')
                        {
                            // status = "NOTE";
                            htmlString = "<b>SEE NOTES</b>";
                        }

                        return {html : htmlString};
                    },
                    eventClick : function(info){

                        data = info.event.extendedProps;

                        if(data.eventType == 'job')
                        {
                            modalHeading = document.getElementById("modelHeading");
                            eventClientName = document.getElementById("event-client-name");
                            eventDescription = document.getElementById("event-description");
                            eventPONumber = document.getElementById("event-po-number");
                            eventEmployeeName = document.getElementById("event-employee-name");
                            eventComment = document.getElementById("event-comment");
                            invoiceLinkSection = document.getElementById("invoice-link-section");
                            invoiceLink = document.getElementById("invoice-link");

                            jobTitleStatus = '<span class="job-title-status" style="background-color:'
                                            + info.event.backgroundColor + '">'
                                            + data.status + '</span>';

                            modalHeading.innerHTML = "JOB ID #" + info.event.title + jobTitleStatus;
                            eventClientName.value = data.client;
                            eventDescription.value = data.description;
                            eventPONumber.value = data.poNumber;

                            if(data.invoiceUrl)
                            {
                                invoiceLink.href = data.invoiceUrl;
                                invoiceLinkSection.style.display = "unset";
                            } 
                            else
                            {
                                invoiceLinkSection.style.display = "none";
                            }

                            employees = "";

                            if(data.employee.length > 0)
                            {
                                data.employee.forEach(function(item){
                                    employees += item.name + " (" + item.jobTitle + ")\n";
                                });
                            } 

                            eventEmployeeName.value = employees;

                            $('#ajaxModel').modal('show');
                        }
                        else if(data.eventType == 'note')
                        {   
                            noteID = info.event.id;

                            calendarNote = document.getElementById("calendar_note");

                            calendarNote.value = data.message;

                            $('#calendar-note-save').unbind();

                            $('#calendar-note-save').click(function(){

                                note = $('#calendar_note').val();
 
                                newNote = {
                                    'id' : info.event.id,
                                    'message' : note,
                                    'note_date' : data.note_date
                                };

                                $.ajax({
                                    type: "POST",
                                    url: '/calendarnote',
                                    data: newNote,
                                    dataType: 'json',
                                    success: function (data) {

                                        eventTemp = calendar.getEventById(data.id);

                                        eventTemp.setExtendedProp('message', data.message);
                                        eventTemp.setProp('start', data.note_date);

                                        $('#calendar_note').val(data.message);
                                        $('#ajaxNoteModel').modal('hide');
                                        toastr.success('Note edited.');
                                    },
                                    error: function (data) {
                                        toastr.error('Error!');
                                    }
                                });           

                                $(this).unbind();
                            });

                            $('#ajaxNoteModel').modal('show');
                        }
                    },
                    dateClick: function(info){

                        $('#calendar-note-save').unbind();

                        if('{{Auth::user()->roles}}' == 'admin')
                        {
                            calendarNote = $('#calendar_note').val("");

                            $('#ajaxNoteModel').modal('show');

                            $('#calendar-note-save').click(function(){

                                calendarNote = $('#calendar_note').val();

                                if( calendarNote.length > 0)
                                {
                                    newNote = {
                                        'message' : calendarNote,
                                        'note_date' : info.dateStr
                                    };

                                    $.ajax({
                                        type: "POST",
                                        url: '/calendarnote',
                                        data: newNote,
                                        dataType: 'json',
                                        success: function (data) {

                                            newEvent = calendar.getEventById(data.id);

                                            calendar.addEvent({
                                                'id' : data.id,
                                                'title' : 'Noooootes',
                                                'backgroundColor' : '#e6db6c',
                                                'textColor' : '#000',
                                                'start' : info.dateStr,
                                                'extendedProps' : {
                                                    'job_id' : '',
                                                    'eventType' : 'note',
                                                    'message' : data.message,
                                                    'note_date' : data.note_date
                                                }
                                            });

                                            $('#ajaxNoteModel').modal('hide');
                                            toastr.success('Note added.');
                                        },
                                        error: function (data) {
                                            toastr.error('Error!');
                                        }
                                    });
                                }

                                $(this).unbind();
                            });
                        }
                    }
                });

                $('#job-delete').click(function(){
                    deleteJob = confirm("Are you sure you want to delete this job?");

                    if(deleteJob == true) {
                        $.ajax({
                            type: "DELETE",
                            url: '/jobs/'+data.job_id,

                            success: function (data) {
                                event= calendar.getEventById(data.id);

                                event.remove();

                                toastr.success('Deleted successfully!');
                                $('#ajaxModel').modal('hide');
                            },
                            error: function (data) {
                                toastr.error('Error!');
                            }
                        });
                    }
                });

                $('#calendar-note-dete').click(function(){
                    deleteJob = confirm("Delete Note?");

                    if(deleteJob == true) {
                        $.ajax({
                            type: "DELETE",
                            url: '/calendarnote/'+noteID,

                            success: function (data) {

                                event = calendar.getEventById(data.id);
                                event.remove();

                                toastr.success('Note deleted.');
                                $('#ajaxNoteModel').modal('hide');
                            },
                            error: function (data) {
                                toastr.error('Error!');
                            }
                        });
                    }
                });

                calendar.render();
            });

        </script>

    @endpush

@stop
