<?php

/**
 * Custom Admin Routes File
 *
 * This file allows adding custom admin routes without modifying the main admin.php
 * Routes defined here will be loaded by the main admin routes file.
 */

use Illuminate\Support\Facades\Route;

// ─── Admin Appointment Management (Manual Booking / Reschedule / Edit) ──────
Route::group([
    'prefix'     => 'appointments',
    'namespace'  => 'App\Http\Controllers\Admin',
    'middleware' => ['web', 'auth', 'admin'],
], function () {
    Route::get('/create',       'AppointmentsController@create')->name('admin.appointments.create');
    Route::post('/store',       'AppointmentsController@store')->name('admin.appointments.store');
    Route::get('/{id}/edit',    'AppointmentsController@edit')->name('admin.appointments.edit');
    Route::post('/{id}/update', 'AppointmentsController@update')->name('admin.appointments.update');
    Route::get('/get-times',    'AppointmentsController@getTimes')->name('admin.appointments.getTimes');
});