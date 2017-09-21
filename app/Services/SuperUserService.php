<?php

namespace App\Services;

class SuperUserService
{

    public function registerUsers($pages = 25) {
//        try {
            $users = $this->getUsers();
            return $users->paginate($pages);
//        } catch (\Exception $e) {
//            throw $e;
////            echo $exc->getTraceAsString();
//        }
    }

    private function getUsers() {
//        try {
            $users = \DB::connection('mysql_laravel')
                    ->table('users')
                    ->select(\DB::raw('users.id as key_id'))
                    ->leftJoin('sinren_data_db.sinren_users', 'users.id', '=', 'sinren_users.user_id')
                    ->leftJoin('suisin_db.suisin_users', 'users.id', '=', 'suisin_users.user_id')
                    ->leftJoin('roster_data_db.roster_users', 'users.id', '=', 'roster_users.user_id')
            ;
            return $users;
//        } catch (\Exception $e) {
//            throw $e;
//        }
    }

    private function setNull($val) {
        if ($val === 'null')
        {
            return null;
        }
        return $val;
    }

    public function searchUsers($input, $pages = 25) {

//        try {
            $users = $this->getUsers();
            if ($input['name'] != '')
            {
                echo "name";
                $users->where('name', 'like', "%{$input['name']}%");
            }

            if ($input['mail'] != '')
            {
                echo "mail";
                $users->where('email', 'like', "%{$input['mail']}%");
            }

            if ($input['super'] != '')
            {
                echo "super";
                $users->where('is_super_user', '=', $input['super']);
            }

            if ($input['suisin'] != '')
            {
                echo "suisin";
                $users->where('suisin_users.is_administrator', '=', $this->setNull($input['suisin']));
            }

            if ($input['roster'] != '')
            {
                $users->where('roster_users.is_administrator', '=', $this->setNull($input['roster']));
            }

            if ($input['div'] != '')
            {
                $users->where('division_id', '=', $input['div']);
            }
            return $users->paginate($pages);
//        } catch (Exception $e) {
//            throw $e;
//        }
    }

    public function parameter($input) {
//        $input = \Input::get();
        if (empty($input) === true)
        {
            return [
                'name'   => '',
                'div'    => '',
                'mail'   => '',
                'super'  => '',
                'suisin' => '',
                'roster' => '',
            ];
        }
        else
        {
            return [
                'name'   => $input['name'],
                'div'    => $input['div'],
                'mail'   => $input['mail'],
                'super'  => $input['super'],
                'suisin' => $input['suisin'],
                'roster' => $input['roster'],
            ];
        }
    }

//    public function editLaravelUser($input, $id) {
//        try {
//            $user                = \App\User::find($id);
//            $user->is_super_user = (int) $input['is_super_user'];
//            $user->save();
//        } catch (Exception $ex) {
//            
//        }
//    }
//
//    public function editSuisinUser($input, $id) {
//        
//    }

    public function editUser($input, $id) {

        try {
            $validator = \Validator::make($input, ['is_super_user' => 'required|boolean']);
            if ($validator->fails())
            {
                return $validator;
            }
            $user                = \App\User::find($id);
            $user->is_super_user = (int) $input['is_super_user'];
            $user->save();

            if (isset($input['suisin_is_administrator']) && !empty($user->SuisinUser))
            {
                $validator = \Validator::make($input, ['suisin_is_administrator' => 'required|boolean']);
                if ($validator->fails())
                {
                    return $validator;
                }
                $suisin_users = \App\SuisinUser::where('user_id', '=', $id)->get();
                foreach ($suisin_users as $suisin_user) {
                    $suisin_user->is_administrator = $input['suisin_is_administrator'];
                    $suisin_user->save();
                }
            }

            if (isset($input['roster_is_administrator']) && $user->RosterUser($id))
            {
                $roster_users = \App\RosterUser::where('user_id', '=', $id)->get();
                foreach ($roster_users as $roster_user) {
                    $roster_user->is_administrator = $input['roster_is_administrator'];
                    $roster_user->save();
                }
            }
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
