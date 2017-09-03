<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ControlDivision extends Model
{
    protected $connection = 'mysql_sinren';
    protected $table      = 'control_divisions';
    protected $guarded    = ['id'];
    
    public function scopeUsers($query, $user_id){
        return $query->where('user_id', $user_id);
    }
    
    public function Division(){
        return $this->hasOne('\App\Division', 'division_id','division_id');
    }
}
