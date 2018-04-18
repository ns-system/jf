<?php

if (!function_exists('route_with_query'))
{

    function route_with_query($route_name, $route_params = [], $query_params = []) {
        $static_route = (empty($route_params)) ? route($route_name) : route($route_name, $route_params);
        $query_route  = (empty($query_params)) ? $static_route : $static_route . '?' . http_build_query($query_params);
        return $query_route;
    }

}


if (!function_exists('roster_role'))
{

    function roster_role() {
        if (!\Auth::check())
        {
            return false;
        }

        $u = \App\User::where('users.id', '=', \Auth::user()->id)->leftJoin('roster_db.roster_users', 'users.id', '=', 'roster_users.user_id')->first();

        if (empty($u->user_id))
        {
            return "not_register";
        }

        if ($u->is_super_user || $u->is_adminsitrator)
        {
            return "admin";
        }
        if ($u->is_chief)
        {
            return "chief";
        }

        if ($u->is_proxy)
        {
            return ($u->is_proxy_active) ? "proxy_active" : "proxy_inactive";
        }

        return "user";
    }

}

