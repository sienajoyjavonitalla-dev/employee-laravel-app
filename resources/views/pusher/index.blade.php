
@extends('layouts.app')

@section('content')
<div class="chat">


<div class="top">
    <h2 style="text-align:center">Uprise Rigging Channel</h2>
</div>



<div class="messages" id="messages">
  @foreach($messages as $message)

    @if($message->user_id != $user_id)
        @include('pusher.receive', ['message' => $message->message, 'name' => $message->name])            
    @else
        @include('pusher.broadcast', ['message' => $message->message, 'name' => $message->name])
    @endif

  @endforeach
</div>



<div class="bottom">
    <form id="msgForm">
      @csrf
      <input type="text" id="user_id" name="user_id" hidden="true" value="{{Auth::user()->id}}">
      <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
      <button type="submit" id="msgBtn" class="btn btn-secondary" disabled="true">SEND</button>
    </form>
</div>        

</div>
@stop

@section('scripts')

<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<link rel="stylesheet" href="/css/pusherchat.css">

<script>

  const pusher  = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {cluster: 'ap1'});
  const channel = pusher.subscribe('public');

  $('document').ready(function(){

    $("#messages").animate({
          scrollTop: $(
            '#messages').get(0).scrollHeight
    }, 200);

    $("#message").on('input', function(e){

      if($(e.target).val() == '')
      {
        $("#msgBtn").attr('disabled', true);
      } else {
        $("#msgBtn").removeAttr('disabled');
      }
    });

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $('#msgBtn').click(function (e) {

        e.preventDefault();

        $(this).html('Saving..');


        // Broadcast Message
        $.ajax({
          
          data: $('#msgForm').serialize(),
          url: "{{ route('pusher.broadcast') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {
            $('#msgForm').trigger("reset");
            $('#msgBtn').html('Save Changes');

            toastr.success('Sent');
          },
          error: function (data) {

            $('#msgBtn').html('Save Changes');
            toastr.error('Not sent!');
          }

        }).done(function (res) {

          console.log(res);
          $(".messages > .message").last().after(function(){
              return '<div class="right message"><p class="message-row-user">'+ res.message +'</p></div>';
          });
          $("form #message").val('');
          $(document).scrollTop($(document).height());

        });
    });

    // Receive Message
    channel.bind('chat', function (data) {

      toastr.success('Received Message');
      console.log(data);

      $.ajax({

        data: data,
        url: "{{ route('pusher.receive') }}",
        type: "POST",
        dataType: 'json',
        
      }).done(function (res) {

        console.log(res);
        $(".messages > .message").last().after(function(){
            return '<div class="left message"><p><span class="message-user-name">'+ res.name +'</span><span class="message-row">'+ res.message +'</span></p></div>';
        });

        $(document).scrollTop($(document).height());

      });

    });

  });

</script>
@stop