<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

//Clearing cache automatically
Route::get('/clear-all', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');

    return response()->json(['message' => 'All cache cleared']);
});


