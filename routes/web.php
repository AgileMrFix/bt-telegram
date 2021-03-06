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

Route::post('/' . config('telegram.bot_token') . '/webhook', 'WebhookController@processWebhook');

Route::get('set', function () {
    $response = \Telegram\Bot\Laravel\Facades\Telegram::setWebhook([
        'url' => 'https://telegram.tbvat.com.ua/' . config('telegram.bot_token') . '/webhook',
        'certificate' => 'cert.pem'
    ]);
    return $response;
});

Route::get('rem', function () {
    $response = Telegram::removeWebhook();
    return $response;
});

Route::get('send', function () {

    $text = file_get_contents('http://rzhunemogu.ru/RandJSON.aspx?CType=1');
    $text = iconv('CP1251', 'UTF-8', $text);
    $text = preg_replace('/\s\s+/', ' ', $text);
    $text = json_decode($text, true);
$text =    $text['content'];

    $data = [];
    foreach (\App\Models\Telegram\Department::all()->pluck('name')->toArray() as $item) {
        $data[] = [$item];
    }

    $keyboard = $data;

    $reply_markup = [
        'keyboard' => $keyboard,
        'resize_keyboard' => true,
        'one_time_keyboard' => true,

    ];

    $reply_markup = Telegram::replyKeyboardMarkup($reply_markup);
    $response = Telegram::sendMessage(['text' => $text, 'chat_id' => 357906340, 'reply_markup' => $reply_markup]);

    return $response;
});

Route::get('test', 'WebhookController@testUpdate')->name('test.update');

