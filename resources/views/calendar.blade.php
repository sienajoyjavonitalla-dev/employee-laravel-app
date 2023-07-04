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
                            <textarea class="form-control" id="calendar_note" name="calendar_note" rows="3"></textarea>
                        </div>
                    </div>

                    <span id="calendar-note-save" class="btn btn-info calendar-note-btn mr-2 float-right">Save Note</span>
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
                            status = "NOTE";
                            htmlString =    "<b>NOTES</b>";
                        }

                        return {html : htmlString};
                    },
                    eventClick : function(info){

                        data = info.event.extendedProps;

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
                    },
                    dateClick: function(info){

                        if('{{Auth::user()->roles}}' == 'admin')
                        {
                            $('#ajaxNoteModel').modal('show');

                            $('#calendar-note-save').click(function(){

                                var note = $('#calendar_note').val();

                                newNote = {
                                    'message' : note,
                                    'note_date' : info.dateStr
                                };

                                $.ajax({
                                    type: "POST",
                                    url: '/calendarnote',
                                    data: newNote,
                                    dataType: 'json',
                                    success: function (data) {

                                        calendar.addEvent({
                                            'title' : 'Noooootes',
                                            'backgroundColor' : '#e6db6c',
                                            'textColor' : '#000',
                                            'start' : info.dateStr,
                                            'extendedProps' : {
                                                'job_id' : '',
                                                // 'employee' : 'n/a',
                                                // 'client' : 'n/a',
                                                // 'description' : 'n/a',
                                                // 'poNumber' : 'n/a',
                                                // 'status' : 'assigned',
                                                // 'invoiceUrl' : 'n/a',
                                                'eventType' : 'note',
                                                'message' : note
                                            }
                                        });

                                        $('#ajaxNoteModel').modal('hide');
                                        toastr.success('Note added.');
                                    },
                                    error: function (data) {
                                        toastr.error('Error!');
                                    }
                                });                                

                                $('#calendar_note').val('');

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

                calendar.render();
            });

        </script>

    @endpush

@stop
