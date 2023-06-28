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
Route::resource('jobs', App\Http\Controllers\JobsController::class);
Route::resource('users', App\Http\Controllers\UsersController::class);
Route::resource('timelogs', App\Http\Controllers\LogTimeController::class);
Route::resource('clients', App\Http\Controllers\ClientsController::class);
// Route::resource('invoices', InvoiceController::class);
Route::get('invoices', 'App\Http\Controllers\InvoiceController@index')->name('invoices.index');
Route::get('invoices/pdf', 'App\Http\Controllers\InvoiceController@pdf')->name('invoices.pdf');
Route::get('timeclock', 'App\Http\Controllers\LogTimeController@timeclock')->name('timeclock');
Route::post('clock_in_out', 'App\Http\Controllers\LogTimeController@clock_in_out')->name('clock-in-out');
Route::get('calendar', 'App\Http\Controllers\CalendarController@index')->name('calendar');
Route::get('how_tos', 'App\Http\Controllers\UsersController@how_tos')->name('calendar');

Route::get('laravel-signature-pad','App\Http\Controllers\LogTimeController@signature');
Route::get('timesheet', 'App\Http\Controllers\LogTimeController@timesheet')->name('timesheet');

Route::get('client/{client}/show_invoices', 'App\Http\Controllers\ClientsController@show_invoices')->name('client.show-invoices');
Route::get('generate/invoice', 'App\Http\Controllers\JobsController@generateInvoice')->name('job.generate.invoice');

//post methods
Route::post('assign', 'App\Http\Controllers\JobsController@store_assigned')->name('assign.store');
Route::get('assign/{job}', 'App\Http\Controllers\JobsController@assigned_index')->name('assign.index');
Route::post('assign/{assign}/delete', 'App\Http\Controllers\JobsController@assigned_delete')->name('assign.delete');
Route::get('generatePDF', 'App\Http\Controllers\InvoiceController@generatePDF')->name('invoices.generate.pdf');

//xero
Route::get('xero/authorize', 'App\Http\Controllers\XeroController@get_started')->name('xero.get_started');
Route::get('xero/callback', 'App\Http\Controllers\XeroController@callback')->name('xero.callback');
Route::get('xero/connections', 'App\Http\Controllers\XeroController@connections')->name('xero.connections');
Route::get('xero/get_invoices', 'App\Http\Controllers\XeroController@get_invoices')->name('xero.get_invoices');
