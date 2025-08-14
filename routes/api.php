<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;

Route::get('/blogs', [BlogController::class, 'index']);
