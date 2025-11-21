<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\ImportCsvController;


Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::resource('import', ImportCsvController::class)->only('create', 'store');
    Route::resource('sendMail', SendMailController::class);

});