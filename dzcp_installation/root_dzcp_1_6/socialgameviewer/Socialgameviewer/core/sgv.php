<?php

abstract class Sgv {

    private $settings;
    protected $players;

	private $server_overloaded = false;

    public function __construct($data) {
		$this->settings = $data;
		$this->load_players();
    }

    public function get_steam64_id($str) {
        $ret = false;
        $data = strtolower(trim($str));
        if ($data != '') 
        {
            if (is_numeric($data) & ereg('7656119', $data)) {
                $ret = $data;
            } else if (substr($data,0,7) == 'steam_0') {
                $tmp = explode(':',$data);
                if ((count($tmp) == 3) && is_numeric($tmp[1]) && is_numeric($tmp[2]))
                {							
                    $friendid = ($tmp[2]*2)+$tmp[1]+1197960265728;
                    $friendid = '7656'.$friendid;
                    $ret = $friendid;
                }
            } else {
                $steam_profile = simplexml_load_file("http://steamcommunity.com/id/".str_replace('steam_','ERROR_POFILE_FIXED',$data)."/?xml=1");
                $ret = $steam_profile->steamID64;
            }
        }
        return $ret;
    }

    public function render_viewer() {
		global $dir;
        $disp = "";
		$count = 0;
		
		$modul_name = $this->settings->modul;
		$modul_file = $modul_name.'.php';
		$modul_path = $dir.'/module/'.$modul_name.'/';
		
		if (file_exists($modul_path.$modul_file)) {
			include $modul_path.$modul_file;
		} else {
			die ('Modul not found: '.$modul_path.$modul_file);
		}
		
		foreach ($this->players as $player) {
			if ($count >= $this->settings->max_disp) break;
			if (!$this->settings->view_offline && !$player['online']) continue;
			if (!$this->settings->view_privat && $player['visibility']) continue;
			$count ++;
			$disp .= render($player, $this->settings);
		}
		
		echo $disp;
		return true;
    }
	
    private function getJson($url) {
		$ctx = stream_context_create(array(
				'http' => array(
						'timeout' => 20,
					)
				));
		if($ret = json_decode(file_get_contents($url, NULL, $ctx))) {
			return $ret;
		} else {
			$this->server_overloaded = true;
		}
		return false;
    }
	
	private function get_xml() {
		if($ret = simplexml_load_file($xml)) {
			return $ret;
		} else {
			$this->server_overloaded = true;
		}
		return false;
	}

    private function get_steam_player_informations($comids = array()) {
        $players = implode(",",(array)$comids);
        return $this->getJson(
			"http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$this->settings->steam_api_key."&steamids=".$players
		);
    }
    
    public function load_player_informations($comids) {
		$players = $this->get_steam_player_informations($comids)->response->players;
		
		foreach ($players as $player) {
			$this->players[$player->steamid]['comid'] = $player->steamid;
			$this->players[$player->steamid]['url'] = $player->profileurl;
			$this->players[$player->steamid]['online'] = $player->personastate > 0 ? 1 : 0;
			$this->players[$player->steamid]['visibility'] = $player->communityvisibilitystate < 2 ? 0 : 1;
			$this->players[$player->steamid]['nickname'] = $player->personaname;
			$this->players[$player->steamid]['avatar'] = $player->avatarfull;
		}
    }
    
    public abstract function load_players();

}