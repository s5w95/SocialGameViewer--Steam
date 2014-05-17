<?php

class Sgv_steam_group extends Sgv {

    private $group = 'hd-gamers-de';
    
    public function set_group($group){
        $this->group = $group;
    }
    
    public function load_players() {
		$id = $this->group;
        if (is_numeric($id)) {
            $xml = "http://steamcommunity.com/gid/$id/memberslistxml/?xml=1";
        } else {
            $xml = "http://steamcommunity.com/groups/$id/memberslistxml/?xml=1";
		}
		
		if($group = simplexml_load_file($xml)->members->steamID64) {
			$this->load_player_informations($group);
		} else {
			$this->server_overloaded = true;
		}
    }
}