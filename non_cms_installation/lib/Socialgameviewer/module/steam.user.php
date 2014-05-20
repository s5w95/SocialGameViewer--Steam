<?php

/**
* SocialGameViewer - User Image Generator
* created by Tune389
*/

$image = new SteamImage($_GET['comid']);
$image->render();

class SteamImage {
    
    private $user;
    private $background = 'steambak.png';
    private $color;
    private $img;
    
    public function __construct($steamid) {
        $this->user = new SteamUser($steamid);
        $this->img = @ImageCreateFromPNG($this->background);
    }
    
    public function render() {
        $img = $this->img;
        $this->set_status($img);
        $this->render_forms($img);
        $this->set_avatar($img, $this->user->avatarmedium);
        $this->set_username($img, $this->user->personaname);
        header( "Content-type: image/png" );
        imagepng($img);
        imagecolordeallocate($this->color, 1);
        imagedestroy($img);
    }
    
    private function render_forms($img) {
        imagefilledrectangle($img, 5, 5, 55, 55, $this->color);
    }
    
    private function set_status($img) {

        if (isset($this->user->gameid)) {
            $this->set_game_image($img, $this->user->get_game_image($this->user->gameid));
            $this->set_ingame($img);
        } else {
            $status = $this->user->personastate;
            switch ($status) {
                case 0:
                    $this->set_offline($img);
                    break;
                case 1: case 2: case 3:
                $this->set_online($img);
                break;
            }
        }
    }
    
    private function set_online($img) {
        $this->color = imagecolorallocate( $img, 123, 175, 214 );
        imagestring( $img, 4, 60, 20, 'Zurzeit Online', $this->color );
    }
    
    private function set_offline($img) {
        $this->color = imagecolorallocate( $img, 112, 108, 107 );
            imagestring( $img, 4, 60, 20, 'Zurzeit Offline', $this->color );
    }
    
    private function set_ingame($img) {
        $this->color = imagecolorallocate( $img, 155, 200, 97 );
        imagestring( $img, 4, 60, 20, 'Zurzeit Im Spiel', $this->color );
    }
    
    public function set_avatar($img, $url) {
        $avatar = @ImageCreateFromJPEG($url);
        imagecopyresized($img, $avatar, 8,8,0,0,45,45,64,64);
    }

    private function set_game_image($img, $url) {
        $game = @ImageCreateFromJPEG($url);
        imagecopyresized($img, $game, 115,0,0,0,160,60,184,69);
        $game = @ImageCreateFromPNG('over_game.png');
        imagecopyresized($img, $game, 115,0,0,0,160,60,160,60);
    }
    
    public function set_username($img, $username) {
        imagestring( $img, 4, 60, 5, $username, $this->color );
    }
}

class SteamUser {

    private $steamid;
    private $games;

    public function __construct($steamid) {
        $this->steamid = $steamid;
        $user = $this->get_steam_player_informations($steamid)->response->players[0];
        $this->load_last_games($steamid);
        foreach ($user as $key => $value){
            $this->$key = $value;
        }
    }

    function get_game_hash($appid) {
        $games = $this->games->response->games;
        foreach ($games as $game) {
            if ($game->appid == $appid) {
                return $game->img_logo_url;
            }
        }
        return false;
    }

    function get_game_image($appid) {
        return "http://media.steampowered.com/steamcommunity/public/images/apps/$appid/".$this-> get_game_hash($appid).".jpg";
    }

    function load_last_games($steamid) {
        $this->games = ($this->games === NULL) ? $this->getJson(
            "http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=90245BB467E201DE99CF36C6FD1ED9FA&steamid=$steamid"
        ) : $this->games;
    }

    function get_steam_player_informations($steamid) {
        return $this->getJson(
            "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=90245BB467E201DE99CF36C6FD1ED9FA&steamids=".$steamid
        );
    }

    function getJson($url) {
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
}
