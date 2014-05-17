<?php

$sgv = (object) array();

function sgv_set_language($lang) {
	$sgv['lang'] = $lang;
}

function sgv_enable_phpfastcache($bl, $class_path) {
	$sgv['phpfastcache'] = $bl;
	$sgv['phpfastcache_path'] = $class_path;
}

function sgv_set_max_disp($ct) {
	$sgv['max_disp'] = $ct;
}

function sgv_set_steam_api_key($key) {
	$sgv['steam_api_key'] = $key;
}

function sgv_show_offline($b) {
	$sgv['view_offline'] = $b;
}

function sgv_show_addfriend($b) {
	$sgv['view_addfriend'] = $b;
}

function sgv_show_newtab($b) {
	$sgv['view_newtab'] = $b;
}

// Classes
$dir = basename(__DIR__);
include $dir.'/core/sgv.php';
include $dir.'/core/sgv.dzcp.php';
include $dir.'/core/sgv.steam_group.php';