<?php

use app\classes\TwigGlobal; 

TwigGlobal::set('logged_in', $_SESSION['is_logged_in'] ?? '');
TwigGlobal::set('user', $_SESSION['user_logged_data'] ?? '');
TwigGlobal::set('base_path', (function () {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $uri = (string) parse_url('http://a' . $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (stripos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
        return $_SERVER['SCRIPT_NAME'];
    }
    if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
        return $scriptDir;
    }
    return '';
})());

TwigGlobal::set('only_base_path', (function () {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $uri = (string) parse_url('http://a' . $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (stripos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
        return $_SERVER['SCRIPT_NAME'];
    }
    if ($scriptDir !== '/' && stripos($uri, $scriptDir) === 0) {
        return $scriptDir;
    } 
    if ($scriptDir == '/' ) {
        return '/';
    } 

    return '';
})());
TwigGlobal::set('phpversion', phpversion());


