<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{

    use Authenticatable,
        Authorizable,
        CanResetPassword;

    protected $connection = 'mysql_laravel';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'unencrypt_password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function SuisinUser() {
        return $this->hasOne('App\SuisinUser', 'user_id', 'id');
    }

//    public function ConsignorGroupModifyUser() {
//        return $this->belongsTo('App\ConsignorGroup', 'modify_user_id', 'id');
//    }
//
//    public function ConsignorGroupCreateUser() {
//        return $this->belongsTo('App\ConsignorGroup', 'create_user_id', 'id');
//    }

    public function SinrenUser() {
        return $this->hasOne('\App\SinrenUser', 'user_id', 'id');
    }

    public function RosterUser($id) {
        $roster_user = \DB::connection('mysql_laravel')
                ->table('users')
                ->join('sinren_data_db.sinren_users', 'sinren_users.user_id', '=', 'users.id')
                ->join('roster_data_db.roster_users', 'roster_users.user_id', '=', 'users.id')
                ->where('users.id', $id)
                ->first()
        ;
        return $roster_user;
    }

//    public function ControlDivisions() {
//        return $this->hasMany('\App\ControlDivision', 'user_id', 'user_id');
//    }

}
