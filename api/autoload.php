<?php
define('DIR_ROOT', str_replace('\\', '/', dirname(__FILE__)));

spl_autoload_register(function ($class) {
    $norm_name = trim(str_replace('_','/',str_replace('\\','/',$class)),'/');
    $file_name = $norm_name.'.class.php';
    $file_path = DIR_ROOT. '/classes/' . $file_name;
    if ( file_exists( $file_path ) ) {
        return require_once( $file_path );
    }
    $file_path = DIR_ROOT. '/common/' . $file_name;
    if ( file_exists( $file_path ) ) {
        return require_once( $file_path );
    }
    $file_path = DIR_ROOT. '/library/' . $file_name;
    if ( file_exists( $file_path ) ) {
        return require_once( $file_path );
    }
    $start = intval(strrpos($norm_name, '/'));
    $file_name = trim(substr($norm_name, $start),'/').'.php';
    $file_path = DIR_ROOT. '/util/' . $file_name;
    if ( file_exists( $file_path ) ) {
        return require_once( $file_path );
    }
});