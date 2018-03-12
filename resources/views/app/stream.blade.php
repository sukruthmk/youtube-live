@extends('app.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h3 style="margin-top: 2%;"> {{$broadcast_title}} </h3>
            </div>
        </div>
    </div>
    <input id="live-stream-id" name="live-stream-id" type="hidden" value="{{$live_stream_id}}">
    <div class="container">
        <div class="row">
            <div class="col">
                {!! $broadcast_html !!}
            </div>
            <div class="col">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <h4>
                                Chats
                                &nbsp;&nbsp;<a href="live-stream?id={{$broadcast_id}}&liveChatId={{$live_stream_id}}"><button id="refresh" class="btn btn-outline-success input-group-button" type="button" >Refresh</button></a>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="container message-container">
                    @foreach ($chats as $chat)
                        <div class="row message-div">
                            <div class="col">
                                <span>
                                   <img src="{{$chat['authorDetails']['profileImageUrl']}}" height="30" width="30">
                               </span>
                               <span>
                                   {{$chat['authorDetails']['displayName']}} &nbsp;&nbsp;
                               </span>
                               <span class="message">
                                   {{$chat['snippet']['displayMessage']}}
                               </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <br>
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <input id="post-msg" name="post-msg" type="text" class="form-control" placeholder="Enter your msg">
                                <div class="input-group-append">
                                    <button id="post" class="btn btn-outline-success input-group-button" type="button" >Post</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="input-group mb-3">
                    <input id="search-user" name="search-user" type="text" class="form-control" placeholder="Search..." aria-label="Search..." aria-describedby="search-button">
                    <div class="input-group-append">
                        <button class="btn btn-outline-success input-group-button" id="search-button">search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table serach-table">
                    <thead>
                        <th> Name </th>
                        <th> Message </th>
                    </thead>
                    <tbody class="search-body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="row">
            <div id="highcharts-container" class="col">
            </div>
        </div>
    </div>
@endsection
