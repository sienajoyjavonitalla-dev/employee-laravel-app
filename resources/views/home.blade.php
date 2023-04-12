@extends('adminlte::page')
@section('title', 'Dashboard')

<!-- @if(session()->has('alert'))
    <script>
        alert({{ session()->get('alert') }});
    </script>
@endif  -->
@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
<!-- @if(session('alert')) -->
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="alert alert-success" role="alert">i am an alert</div>
        </div>
    </div>
<!-- @endif  -->
<div class="row">
<div class="col-lg-6 align-items-center border">
    <span class=" ">JOBS</span>

</div>
<div class="col-lg-6 align-items-center border">
    ADD JOB
    <form method="post" action="/jobs" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="clientid" class="col-sm-3 col-form-label">Client</label>
            <div class="col-sm-9">
                <input name="client_id" type="text" class="form-control" id="clientid">
            </div>
        </div>
        <div class="form-group row">
            <label for="titleid" class="col-sm-3 col-form-label">Title</label>
            <div class="col-sm-9">
                <input name="title" type="text" class="form-control" id="titleid">
            </div>
        </div>
        <div class="form-group row">
            <label for="description" class="col-sm-3 col-form-label">Description</label>
            <div class="col-sm-9">
                <input name="description" type="text" class="form-control" id="description">
            </div>
        </div>
        <div class="form-group row">
            <label for="start_date_time" class="col-sm-3 col-form-label">Start Date & Time</label>
            <div class="col-sm-9">
                <input name="start_date_time" type="text" class="form-control" id="start_date_time">
            </div>
        </div>
        <div class="form-group row">
            <label for="end_date_time" class="col-sm-3 col-form-label">End Date & Time</label>
            <div class="col-sm-9">
                <input name="end_date_time" type="text" class="form-control" id="end_date_time">
            </div>
        </div>
        <div class="form-group row">
            <label for="job_address" class="col-sm-3 col-form-label">Address</label>
            <div class="col-sm-9">
                <input name="job_address" type="text" class="form-control" id="job_address">
            </div>
        </div>
        <!-- <div class="form-group row">
            <label for="gameimageid" class="col-sm-3 col-form-label">Game Image</label>
            <div class="col-sm-9">
                <input name="image" type="file" id="gameimageid" class="custom-file-input">
                <span style="margin-left: 15px; width: 480px;" class="custom-file-control"></span>
            </div>
        </div> -->
        <div class="form-group row">
            <div class="offset-sm-3 col-sm-9">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
</div>
@stop