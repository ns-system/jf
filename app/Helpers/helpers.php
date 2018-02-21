<?php

if (!function_exists('route_with_query'))
{

    function route_with_query($route_name, $route_params = [], $query_params = []) {
        $static_route = (empty($route_params)) ? route($route_name) : route($route_name, $route_params);
        $query_route  = (empty($query_params)) ? $static_route : $static_route . '?' . http_build_query($query_params);
        return $query_route;
    }

}