<?php

namespace App\Services;

class SuperUserService
{

    public function getRegisterUsers($pages = 25) {
        $users = $this->getUsers();
        return $users->paginate($pages);
    }

    private function getUsers() {
        $users = \App\User::select(\DB::raw('*, users.id as id, sinren_users.user_id as sinren_user_id'))
                ->addSelect(\DB::raw('roster_users.user_id as roster_user_id, roster_users.is_administrator as is_roster_admin'))
                ->addSelect(\DB::raw('sinren_users.division_id as division_id'))
                ->leftJoin('sinren_db.sinren_users', 'users.id', '=', 'sinren_users.user_id')
                ->leftJoin('sinren_db.sinren_divisions', 'sinren_users.division_id', '=', 'sinren_divisions.division_id')
//                ->leftJoin('suisin_db.suisin_users', 'users.id', '=', 'suisin_users.user_id')
                ->leftJoin('roster_db.roster_users', 'users.id', '=', 'roster_users.user_id')
                ->orderBy('sinren_users.division_id', 'asc')
        ;
        return $users;
    }

    private function setNull($val) {
        if ($val === 'null')
        {
            return null;
        }
        return $val;
    }

    public function searchUsers($input, $pages = 25) {

        $users = $this->getUsers();
        if ($input['name'] !== '')
        {
//            $users->where('name', 'like', "%{$input['name']}%");
            $users->where('last_name', 'like', "%{$input['name']}%");
        }

        if ($input['mail'] !== '')
        {
            $users->where('email', 'like', "%{$input['mail']}%");
        }

        if ($input['super'] !== '')
        {
            $users->where('is_super_user', '=', $input['super']);
        }

        if ($input['roster'] !== '')
        {
            $users->where('roster_users.is_administrator', '=', $this->setNull($input['roster']));
        }

        if ($input['div'] !== '')
        {
            $users->where('sinren_users.division_id', '=', $input['div']);
        }
        return $users->paginate($pages);
    }

    public function parameter($input) {
//        $input = \Input::get();
        if (empty($input))
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
                'roster' => $input['roster'],
            ];
        }
    }

    public function editUser($input, $id) {

        try {
            $user                = \App\User::find($id);
            $user->is_super_user = (int) $input['is_super_user'];
            $user->save();

//            if (isset($input['suisin_is_administrator']) && !empty($user->SuisinUser))
//            {
//                $suisin_users = \App\SuisinUser::where('user_id', '=', $id)->get();
//                foreach ($suisin_users as $suisin_user) {
//                    $suisin_user->is_administrator = $input['suisin_is_administrator'];
//                    $suisin_user->save();
//                }
//            }

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
