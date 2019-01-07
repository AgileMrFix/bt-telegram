<?php

namespace App\Models\Telegram;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Telegram\Role
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Telegram\Role query()
 * @mixin \Eloquent
 */
class Role extends Model
{
    protected $fillable = ['name'];
}
