<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| that contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to admin
Route::get('/', function () {
    return redirect('/admin');
});

// Example of additional public-facing routes
// You can add other routes here for the front-end of your application

// Home Page Route
Route::get('/home', function () {
    return view('home');
})->name('home');

// Contact Page Route
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
