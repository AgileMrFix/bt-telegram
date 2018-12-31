<?php

namespace App\Models\Telegram;

use App\MessageHistory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Telegram\TelegramUser
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\TelegramUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\TelegramUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\TelegramUser query()
 * @mixin \Eloquent
 */
class TelegramUser extends Model
{
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'username',
        'is_bot'
    ];

    public function message_histories()
    {
        return $this->hasMany(MessageHistory::class);
    }
}
