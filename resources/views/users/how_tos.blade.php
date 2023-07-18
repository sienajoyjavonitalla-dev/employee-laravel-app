@extends('layouts.app')

@section('content')
<div class="list-group">
  <a href="https://docs.google.com/document/d/1xE6pHcaDXwy49w2cEW36TGTfG559yVYRhNj1Jhtd__4/edit?usp=sharing" target="_blank" class="list-group-item list-group-item-action">
    How to Clock In/Out
  </a>
  @if(auth()->user()->roles == 'admin' || auth()->user()->roles == 'subadmin')
    <a href="https://docs.google.com/document/d/1QiqlWt2AnJfB5Jc509Boq5oA42nIfVR26PFOIQyZsaU/edit?usp=sharing" target="_blank" class="list-group-item list-group-item-action">How to create a job and assign to persons</a>
    <a href="https://docs.google.com/document/d/1wSZkQ57qe0gC1xlbIC1DWdIn9kEFKcEIT1fKYWU3EVg/edit?usp=sharing" target="_blank" class="list-group-item list-group-item-action">How to generate invoice for a client once job is completed</a>
    <a href="https://docs.google.com/document/d/1Di01GBt2TQZRO_xQSdhwHfG_CFdMKMAu80Ea33Prwq0/edit?usp=sharing" target="_blank" class="list-group-item list-group-item-action">How to create invoice for the subcontractor</a>


  @endif
</div>

@stop
