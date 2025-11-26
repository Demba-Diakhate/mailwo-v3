<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\ImportCsvController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    
    Route::resource('import', ImportCsvController::class)->only('create', 'store');
    Route::resource('sendMail', SendMailController::class);
});