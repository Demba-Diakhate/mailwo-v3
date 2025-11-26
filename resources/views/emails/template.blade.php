@extends('emails.layout')
@section('content')
<div>
    <h1 style="color:red;">TEST TEMPLATE</h1>

    <div class="container">

        <!-- HEADER -->
        <div class="header">
            <!-- Logo -->
            <img src="https://wommate.tech/img/logo_principal.png" alt="Wommate">
            <div class="title">{{ $subject }}</div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            {!! nl2br(e($body)) !!}
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Wommate — Apprendre. Pratiquer. Réussir.<br>
            Thiès, Sénégal — www.wommate.com  
        </div>

    </div>
</div>
@endsection

