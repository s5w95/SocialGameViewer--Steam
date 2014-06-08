<?php

class Sgv_dzcp extends Sgv {

    private $uid = array();

    public function load_players() {
        global $sql_prefix;
        $prefix = $sql_prefix;
        $ples = array();
        do {
            $instand_reload = false;
            $qry = db('SELECT t1.comid, t1.userid, t2.id, t2.steamid steamid, t1.steamid bakid, t2.level FROM '.$prefix.'socialgameviewer_users t1 RIGHT JOIN '.$prefix.'users t2 ON (t2.id = t1.userid)');
            while ($user = mysqli_fetch_assoc($qry)) {
                if ($user['comid'] == NULL & $user['level'] > 2) {
                    if ($comid = $this->get_steam64_id($user['steamid'])) {
                        db('INSERT INTO '.$prefix."socialgameviewer_users (comid, userid, steamid) VALUES ('".$comid."', '".$user['id']."', '".$user['steamid']."');");
                        $instand_reload = true;
                    }
                }
                if ($user['comid'] != NULL & $user['level'] > 2) {
                    $ples[] = $user['comid'];
                    $this->uid[$user['comid']] = $user['id'];
                }
                if ($user['bakid'] != $user['steamid'] & !empty($user['steamid']) &  !empty($user['bakid'])) {
                    db('delete FROM '.$prefix.'socialgameviewer_users WHERE userid = '.$user['userid']);
                }
            }
        } while ($instand_reload);
        $this->load_player_informations($ples);
    }

    public function get_profile_url($comid) {
        if ($this->settings->view_steamlink) {
            return "http://steamcommunity.com/profiles/$comid/";
        } else {
            return "/user/?action=user&id=".$this->uid[$comid];
        }
    }
}