<?php

class Sgv_steam_group extends Sgv {
    
    public function load_players() {
		$id = $this->settings->group;
        $xml = SgvCache::get('group_xml_'.$id,$this->settings->cache_delay);
        if ($xml === NULL) {
            $xml = "http://steamcommunity.com/groups/$id/memberslistxml/?xml=1";
            $xml = file_get_contents($xml);
            SgvCache::set($xml, 'group_xml_'.$id);
        }
        if($group = simplexml_load_string($xml)->members->steamID64) {
            $this->load_player_informations($group);
        } else {
            $this->server_overloaded = true;
        }
    }
}

function sgv_set_group($group) {
    global $sgv;
    $sgv->group = $group;
}