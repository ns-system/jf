<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $connection = 'mysql_laravel';
    protected $table      = 'notifications';
    protected $guarded    = ['id'];

    public function user() {
        return $this->hasOne('App\User','id','user_id');
    }

    public function scopeDeadline($query, $deadline) {
        return $query->where('deadline', '>=', $deadline);
    }

}
