@extends('layouts.app')

@section('content')
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
                    <p class="col-sm-6 justify-content-end d-flex">PO# {{$job->po_number}}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

@stop
