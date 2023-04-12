@extends('layouts.app')
@extends('adminlte::page')


@section('title', 'Dashboard')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
@if(session()->has('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong><i class="fa fa-check-circle mr-1"></i>Success!</strong> {{session('message')}}.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif
<div class="row">
<div class="col-lg-6 align-items-center border m-2 rounded-4 p-4">
    <h5 class="text-center">JOBS</h5>
    @foreach($jobs as $job)
        <div class="border rounded-4 pl-3 pr-3 pt-3 mb-2">
            <div class="row">
                <h5 class="col-sm-6">{{$job->title}}</h5>
                <p class="text-uppercase col-sm-6 justify-content-end d-flex"><span class="border rounded-5 bg-success pr-2 pl-2">{{$job->status}}</span></p>
            </div>
            <div class="row">
                <p class="p-2 col-sm-6"> Address: {{$job->address}}</p>
                <p class="col-sm-6 justify-content-end d-flex">#{{$job->po_number}}</p>
            </div>

        </div>
    @endforeach
</div>
<div class="col-lg-5 align-items-center border m-2 rounded-4 p-4">
    <h5 class="text-center">ADD JOB</h5>
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
            <label for="po_number" class="col-sm-3 col-form-label">PO Number</label>
            <div class="col-sm-9">
                <input name="po_number" type="text" class="form-control" id="po_number">
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
            <label for="address" class="col-sm-3 col-form-label">Address</label>
            <div class="col-sm-9">
                <input name="address" type="text" class="form-control" id="address">
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
