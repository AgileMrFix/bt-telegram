<?php

namespace App;

use App\Models\Telegram\Department;
use App\Models\Telegram\TelegramUser;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];

    public function telegram_user()
    {
        return $this->belongsTo(TelegramUser::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
