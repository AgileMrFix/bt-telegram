<?php

namespace App;

use App\Models\Telegram\Department;
use App\Models\Telegram\TelegramUser;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    const TYPE_WAIT = 'wait';

    const TYPE_EMPLOYEE = 'employee';
    const TYPE_DEPARTMENT = 'department';

    protected $guarded = [
        'id'
    ];

    public function telegram_user()
    {
        return $this->belongsTo(TelegramUser::class);
    }


    public static function getDataForEmployee($action)
    {
        $data = [
            0 => [
                'name' => 'first_name',
                'message' => "Ваше ім'я:",
                'keyboard' => null,
            ],
            1 => [
                'name' => 'last_name',
                'message' => "Ваше прізвище:",
                'keyboard' => null,
            ],
            2 => [
                'name' => 'department_id',
                'message' => "Оберіть відділ:",
                'keyboard' => self::array2keyboard(
                    Department::all()->pluck('name')->toArray()
                ),
            ]
        ];

        return $data[$action];
    }

    protected static function array2keyboard($arr)
    {
        $data = [];
        foreach ($arr as $item) {
            $data[] = [$item];
        }

        $keyboard = $data;
        return $keyboard;
    }


}
