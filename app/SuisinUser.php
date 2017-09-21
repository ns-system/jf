<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuisinUser extends Model
{

    protected $connection = 'mysql_suisin';
    protected $table      = 'suisin_users';
    protected $guarded    = ['id'];

    public function Users() {
        return $this->belongsTo('App\User', 'email', 'email');
    }

    public function scopeUser($query, $user_id) {
        return $query->where('user_id', '=', $user_id);
    }

}
