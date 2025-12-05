<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\ImportCsvController;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    
    Route::resource('import', ImportCsvController::class)->only('create', 'store');
    Route::resource('sendMail', SendMailController::class);

});

Route::get('/mail-progress', function () {
    return response()->json(Cache::get('mail_progress', [
        'total' => 0,
        'sent'  => 0
    ]));
});