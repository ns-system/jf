<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RosterUser extends Model
{

    protected $connection = 'mysql_roster';
    protected $table      = 'roster_users';
    protected $guarded    = ['id'];

    public function scopeUser($query, $user_id = '') {
        if ($user_id === '')
        {
            if (\Auth::check())
            {
                $user_id = \Auth::user()->id;
            }
        }
        return $query->where('user_id', '=', $user_id);
    }

}
