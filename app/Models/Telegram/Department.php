<?php

namespace App\Models\Telegram;

use App\Employee;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Telegram\Departament
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\Department query()
 * @mixin \Eloquent
 */
class Department extends Model
{
    protected $guarded = ['id'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
