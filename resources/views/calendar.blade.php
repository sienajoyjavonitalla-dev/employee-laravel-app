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

                        <div class="form-group">
                            <label for="address" class="col-sm-6 control-label">Status</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="event-status" name="event-status" value="" maxlength="50" disabled="true">
                            </div>
                        </div>
                    </form>

                    <hr>
                    <p class="invoice-link btn btn-info" id="invoice-link-section"><a href="#" id="invoice-link" target="_blank">INVOICE LINK</a></p>
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
                    height: "98vh",
                    dayMaxEvents: 1,
                    eventContent : function(info){

                        data = info.event.extendedProps;
                        status = "ASSIGNED";

                        if(data.status == "open") status = "OPEN";
                        if(data.status == "completed") status = "COMPLETE";

                        htmlString =    "<b>#" + data.job_id + "</b> " + " - <i>" + status + "</i><br>" +
                                        data.client;

                        return {html : htmlString};
                    },
                    eventClick : function(info){

                        data = info.event.extendedProps;

                        modalHeading = document.getElementById("modelHeading");
                        eventClientName = document.getElementById("event-client-name");
                        eventDescription = document.getElementById("event-description");
                        eventPONumber = document.getElementById("event-po-number");
                        eventEmployeeName = document.getElementById("event-employee-name");
                        eventStatus = document.getElementById("event-status");
                        eventComment = document.getElementById("event-comment");
                        invoiceLinkSection = document.getElementById("invoice-link-section");
                        invoiceLink = document.getElementById("invoice-link");


                        jobTitleStatus = '<span class="job-title-status" style="background-color:'
                                        + info.event.backgroundColor + '">'
                                        + data.status + '</span>';

                                    
                        modalHeading.innerHTML = "JOB ID #" + info.event.title + jobTitleStatus;
                        eventClientName.value = data.client;
                        eventStatus.value = data.status;
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
                });

                calendar.render();
            });

        </script>

    @endpush

@stop
