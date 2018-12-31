<?php

namespace App;

use App\Models\Telegram\TelegramUser;
use Illuminate\Database\Eloquent\Model;

class MessageHistory extends Model
{
    protected $guarded = ['id'];

    public function telegram_user()
    {
        return $this->belongsTo(TelegramUser::class);
    }
}
