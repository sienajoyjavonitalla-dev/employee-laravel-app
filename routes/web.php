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
Route::resource('banks', App\Http\Controllers\BankDetailsController::class);
Route::resource('clients', App\Http\Controllers\ClientsController::class);
Route::resource('calendarnote', App\Http\Controllers\CalendarNoteContoller::class);

# Route::resource('invoices', InvoiceController::class);
Route::get('invoices', 'App\Http\Controllers\InvoiceController@index')->name('invoices.index');
Route::get('subinvoices', 'App\Http\Controllers\InvoiceController@index')->name('subinvoices.index');
Route::get('clients_invoice', 'App\Http\Controllers\InvoiceController@clients_invoice')->name('clients_invoice');
Route::post('subbies_invoice/{id}/delete', 'App\Http\Controllers\InvoiceController@delete')->name('subbies_invoice.delete');

Route::get('invoices/pdf', 'App\Http\Controllers\InvoiceController@pdf')->name('invoices.pdf');

# Timeclock
Route::get('timeclock', 'App\Http\Controllers\LogTimeController@timeclock')->name('timeclock');
Route::post('clock_in_out', 'App\Http\Controllers\LogTimeController@clock_in_out')->name('clock-in-out');
Route::get('timeclock_filter', 'App\Http\Controllers\LogTimeController@timeclock_filter')->name('timeclock.filter');

# Calendar
Route::get('calendar', 'App\Http\Controllers\CalendarController@index')->name('calendar');
Route::get('how_tos', 'App\Http\Controllers\UsersController@how_tos')->name('how_tos');

Route::get('laravel-signature-pad','App\Http\Controllers\LogTimeController@signature');
Route::get('timesheet', 'App\Http\Controllers\LogTimeController@timesheet')->name('timesheet');
Route::get('fulltimetimesheet', 'App\Http\Controllers\LogTimeController@timesheet')->name('fulltimetimesheet');

Route::get('subtimesheet', 'App\Http\Controllers\LogTimeController@timesheet')->name('subtimesheet');


Route::get('client/{client}/show_invoices', 'App\Http\Controllers\ClientsController@show_invoices')->name('client.show-invoices');
Route::get('generate/invoice', 'App\Http\Controllers\JobsController@generateInvoice')->name('job.generate.invoice');
Route::get('void/invoice', 'App\Http\Controllers\InvoiceController@voidInvoice')->name('invoice.void.invoice');

# User Files
Route::post('storefiles', 'App\Http\Controllers\UsersController@store_user_files')->name('user-files.store');
Route::get('userfiles/{user}', 'App\Http\Controllers\UsersController@user_files_index')->name('user-files.index');
Route::post('userfiles/{file}/delete', 'App\Http\Controllers\UsersController@user_files_delete')->name('user-files.delete');

# Jobs
Route::post('assign', 'App\Http\Controllers\JobsController@store_assigned')->name('assign.store');
Route::get('assign/{job}', 'App\Http\Controllers\JobsController@assigned_index')->name('assign.index');
Route::post('assign/{assign}/delete', 'App\Http\Controllers\JobsController@assigned_delete')->name('assign.delete');
Route::post('complete/job', 'App\Http\Controllers\JobsController@complete_job')->name('complete.job');
Route::get('userjobs', 'App\Http\Controllers\JobsController@user_jobs')->name('user.jobs');


# Invoice
Route::get('generatePDF', 'App\Http\Controllers\InvoiceController@generatePDF')->name('invoices.generate.pdf');
Route::get('generateTimesheet', 'App\Http\Controllers\LogTimeController@generateTimesheet')->name('timelogs.generate.timesheet');

# Pusher
Route::get('pusher', 'App\Http\Controllers\PusherController@index')->name('pusher');
Route::post('pusher/receive', 'App\Http\Controllers\PusherController@receive')->name('pusher.receive');
Route::post('pusher/broadcast', 'App\Http\Controllers\PusherController@broadcast')->name('pusher.broadcast');

# Admin Footprints
Route::get('footprints', 'App\Http\Controllers\AdminFootprintController@index')->name('admin.footprint');

Route::get('/testpostmark', 'App\Http\Controllers\AdminFootprintController@testPostmark');

#notes
Route::resource('notes', App\Http\Controllers\NoteController::class);