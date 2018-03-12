@extends('app.main')

@section('content')
<br>
<div class="container">
    <div class="row">
        <div class="col">
            <h3> All Broadcasts </h3>
        </div>
    </div>
</div>
<br>
<div class="container">
    @foreach ($broadcasts as $broadcast)
        <div class="row">
            <div class="col">
                <a href="live-stream?id={{$broadcast['id']}}&liveChatId={{$broadcast['snippet']['liveChatId']}}"> {{$broadcast['snippet']['title']}} </a>
            </div>
        </div>
    @endforeach
</div>


@endsection
