<?php

use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return ['message' => 'API IMS Products Management', 'status' => 200];
});