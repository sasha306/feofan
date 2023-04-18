<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use RuliLG\StableDiffusion\StableDiffusion;
use Telegram\Bot\Laravel\Facades\Telegram;
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



Route::get('/', function () {
    $messages = collect(session('messages', []))->reject(fn ($message) => $message['role'] === 'system');
    return view('site.order', [
        'messages' => $messages
    ]); 
    });

Route::group(['namespace' => 'App\Http\Controllers'], function (){
    
    Route::post('/post/store', 'PostController@store')->name('post.store');
    Route::post('/webhook', 'WebhookController@index');
    
});