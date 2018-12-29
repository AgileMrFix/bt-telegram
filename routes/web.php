<?php

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
    return view('welcome');
});

Route::post('/'.config('telegram.bot_token').'/webhook', function () {

    $updates = \Telegram\Bot\Laravel\Facades\Telegram::getWebhookUpdates();

    return $updates;
});

Route::get('set', function (){
//    $response = \Telegram\Bot\Laravel\Facades\Telegram::setWebhook(['url' => 'https://telegram.tbvat.com.ua/'.env('TELEGRAM_BOT_TOKEN').'/webhook']);
    $response = \Telegram\Bot\Laravel\Facades\Telegram::setWebhook([
        'url' => 'https://telegram.tbvat.com.ua/'.config('telegram.bot_token').'/webhook',
        'certificate' => 'cert.pem'
    ]);
    return $response;
});


