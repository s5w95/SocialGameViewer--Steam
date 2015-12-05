<?php

$sgv = new stdClass();

function sgv_set_language($lang) {
    global $sgv;
	$sgv->lang = $lang;
}

function sgv_caching_size($mode){
    switch($mode) {
        case 'high':
            break;
        case 'medium':
            break;
        case 'low':
            break;
    }
}

function sgv_wipe_interval($interval) {

}

function sgv_set_max_disp($ct) {
    global $sgv;
	$sgv->max_disp = $ct;
}

function sgv_set_steam_api_key($key) {
    global $sgv;
	$sgv->steam_api_key = $key;
}

function sgv_show_offline($b) {
    global $sgv;
	$sgv->view_offline = $b;
}

function sgv_show_addfriend($b) {
    global $sgv;
	$sgv->view_addfriend = $b;
}

function sgv_show_private($b) {
    global $sgv;
    $sgv->view_privat = $b;
}

function sgv_show_newtab($b) {
    global $sgv;
	$sgv->view_newtab = $b;
}

function sgv_set_x_size($i) {
    global $sgv;
    $sgv->x_size = $i;
}

function sgv_set_output_modul($modul) {
    global $sgv;
    $sgv->output_modul = $modul;
}

function sgv_set_relative_path($path) {
    global $sgv;
    $sgv->home = $path;
}

function sgv_set_cache_delay($delay) {
    global $sgv;
    $sgv->cache_delay = $delay;
}

function sgv_set_input_modul($modul) {
    global $sgv;
    $sgv->input_modul = $modul;
}

// Classes
define('SGV_BASE', realpath(dirname(__FILE__)).'/');

include SGV_BASE.'core/sgv.php';
include SGV_BASE.'core/sgv.dzcp.php';
include SGV_BASE.'core/sgv.steam_group.php';
include SGV_BASE.'core/sgv.cache.php';

SgvCache::init();

function sgv_render_viewer() {
    global $sgv;
    if (!isset($sgv->input_modul)) {
        $sgv->input_modul = 'Steam_group';
    }
    $modul = 'Sgv_'.strtolower($sgv->input_modul);
    $sg = new $modul($sgv);
    $sg->render_viewer();
}