<!DOCTYPE html>
<html lang="en">
<head>
  <title>Uprise Chat</title>
  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- JavaScript -->
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <!-- End JavaScript -->

  <!-- CSS -->
  <link rel="stylesheet" href="/css/pusherchat.css">
  <!-- End CSS -->

</head>

<body>
    <div class="chat">

        <!-- Header -->
        <div class="top">
            <h2 style="text-align:center">Uprise Public Chat</h2>
        </div>
        <!-- End Header -->

        <!-- Chat -->
        <div class="messages" id="messages">
            @include('pusher.receive', ['message' => "Hey! What's up!  👋"])
            @include('pusher.receive', ['message' => "Ask a friend to open this link and you can chat with them!"])
        </div>
        <!-- End Chat -->

        <!-- Footer -->
        <div class="bottom">
            <form>
            <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
            <button type="submit" class="btn btn-secondary">SEND</button>
            </form>
        </div>
        <!-- End Footer -->

    </div>
</body>

<script>
  const pusher  = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {cluster: 'ap1'});
  const channel = pusher.subscribe('public');

  //Receive messages
  channel.bind('chat', function (data) {

    $.post("/pusher/receive", {
      _token:  '{{csrf_token()}}',
      message: data.message,
    })
     .done(function (res) {
       $(".messages > .message").last().after(res);

       $("#messages").animate({
          scrollTop: $(
            '#messages').get(0).scrollHeight
      }, 2000);
     });
  });

  //Broadcast messages
  $("form").submit(function (event) {
    event.preventDefault();

    $.ajax({
      url:     "/pusher/broadcast",
      method:  'POST',
      headers: {
        'X-Socket-Id': pusher.connection.socket_id
      },
      data:    {
        _token:  '{{csrf_token()}}',
        message: $("form #message").val(),
      }
    }).done(function (res) {
      $(".messages > .message").last().after(res);
      $("form #message").val('');
      $(document).scrollTop($(document).height());
    });

    $("form #message").val('');
  });

</script>
</html>
