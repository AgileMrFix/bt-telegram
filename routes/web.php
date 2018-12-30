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

Route::post('/' . config('telegram.bot_token') . '/webhook', function () {

    $updates = Telegram::commandsHandler(true);
    $id = $updates['message']['from']['id'];
    $response = Telegram::sendMessage(['text' => 'asd', 'chat_id' => $id]);

    return 'ok';

    return 'ok';
});

Route::get('set', function () {
//    $response = \Telegram\Bot\Laravel\Facades\Telegram::setWebhook(['url' => 'https://telegram.tbvat.com.ua/'.env('TELEGRAM_BOT_TOKEN').'/webhook']);
    $response = \Telegram\Bot\Laravel\Facades\Telegram::setWebhook([
        'url' => 'https://telegram.tbvat.com.ua/' . config('telegram.bot_token') . '/webhook',
        'certificate' => 'cert.pem'
    ]);
    return $response;
});

Route::get('rem', function () {
    $response = \Telegram\Bot\Laravel\Facades\Telegram::removeWebhook();
    return $response;
});

Route::get('send', function () {
    $response = Telegram::sendMessage(['text' => 'asd', 'chat_id' => 357906340]);
    return $response;
});

