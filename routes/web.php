<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\ImportCsvController;
use Illuminate\Support\Facades\Mail;



Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::resource('import', ImportCsvController::class)->only('create', 'store');
    Route::resource('sendMail', SendMailController::class);
});