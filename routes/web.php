<?php

use App\Http\Controllers\ClientsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogTimeController;

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

Route::redirect('/', '/login');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('jobs', JobsController::class);
Route::resource('users', UsersController::class);
Route::resource('timelogs', LogTimeController::class);
Route::resource('clients', ClientsController::class);

Route::get('invoices', 'App\Http\Controllers\InvoiceController@index')->name('invoices.index');
Route::get('timeclock', 'App\Http\Controllers\LogTimeController@timeclock')->name('timeclock');
Route::get('timeclock', 'App\Http\Controllers\LogTimeController@timeclock')->name('timeclock');
Route::post('clock_in_out', 'App\Http\Controllers\LogTimeController@clock_in_out')->name('clock-in-out');