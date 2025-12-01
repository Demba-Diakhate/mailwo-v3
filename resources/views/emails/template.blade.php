@extends('emails.layout')

@section('content')
<div style="background:#ffffff; border-radius:8px;">

    <!-- Title -->
    <h1 style="
        font-size:22px;
        font-weight:700;
        color:#1B97B1;
        margin:0 0 25px 0;
        text-align:center;
    ">
        {{ $subject }}
    </h1>

    <!-- Content container -->
    <div style="
        font-size:16px;
        color:#374151;
        line-height:1.7;
        background:#ffffff;
    ">
        {!! nl2br(e($body)) !!}
    </div>

</div>
@endsection


