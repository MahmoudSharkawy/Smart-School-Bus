<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolDashboardController;

Route::get('/', [SchoolDashboardController::class, 'index']);