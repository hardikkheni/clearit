<?php

// use App\Mail\Ticket\EtaUpdated;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     view('application');
//});

// NOTICE: for testing email templates.
// Route::get('/', function () {
//     $mailData = [
//         'ticketNumber' => '234234234',
//         'firstname' => 'asdasdasd',
//         'etaComment' => 'asdoiyasuidyhauisd',
//         'etaDate' => '',
//         'etaTime' => '',
//         'lastFreeDay' => '',
//     ];
//     return new EtaUpdated($mailData);
// });


Route::get('/{any}', function () {
    return view('application');
})->where('any', '^((?!/api/).)*?');
