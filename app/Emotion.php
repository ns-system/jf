<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emotion extends Model
{
    protected $connection = 'mysql_nikocale';
    protected $table      = 'user_emotions';
    protected $guarded    = ['id'];

}
