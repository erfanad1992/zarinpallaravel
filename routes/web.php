<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;



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


Route::get('/payment', 'HomeController@payment');
Route::get('/verifypayment', 'HomeController@verifypayment');
Route::post('/verifypayment', 'HomeController@verifypayment')->name("payment.send");
Route::post('/payment', 'HomeController@payment')->name("payment.send");
Route::get('/',function (){

    return View('index');
});



