@extends('layouts.app')

@section('content')
    <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;
        var pusher = new Pusher('e8b5e40354edbce88902', {
            cluster: 'ap1',
            encrypted: true
        });

        var channel = pusher.subscribe('order_channel');
        channel.bind('all_orders', function (data) {
            alert(data);
        });
    </script>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        You are logged in!

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
