<?php

use App\Http\Controllers\Api\MeetingController;
use Illuminate\Support\Facades\Route;

Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');
