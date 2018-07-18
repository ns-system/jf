<?php
$base = (!empty($path)) ? $path : '/user_icon/';
$src  = (!empty($avatar)) ? $avatar : 'unset.png';
$size = (!empty($size)) ? $size : '60px';
?>

<style type="text/css">
    .avatar { object-fit: contain; width: {{ $size }}; height: {{ $size }}; background-color: #ddd; border-radius: 50%; border: 1px solid #fff;}
</style>

<img src="{{ asset($base . $src) }}" class="avatar">