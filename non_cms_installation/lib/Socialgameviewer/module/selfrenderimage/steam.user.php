<?php
include ('../../sgv_base.php');
$image = new SteamImage($_GET['comid']);
$image->render();

class SteamImage {
    
    private $user;
    private $background = 'steambak.png';
    private $cache_dir;
    private $color;
    private $img;
    private $font = 'arial';
    
    public function __construct($steamid) {
        $this->cache_dir = SGV_BASE.'_local.cache/';
        $this->steamid = $steamid;
        $this->font = './'.$this->font.'.ttf';
    }
    
    public function render() {
        $file = SgvCache::get($this->steamid.'_last');
        if ($file === NULL) {
            $this->user = new SteamUser($this->steamid);
            $this->img = @ImageCreateFromPNG($this->background);
            $status = $this->user->personastate > 0 ? 1:0;
            if (isset($this->user->gameid)) $status = '2_'.$this->user->gameid;
            $file = $this->cache_dir.$this->steamid.'_'.$status.'.png';

            if (!file_exists($file)) {
                $this->set_status();
                $this->render_forms($this->img);
                $this->set_avatar($this->img, $this->user->avatarmedium);
                $this->set_username($this->img, $this->user->personaname);
                imagepng($this->img,$file);
                //  imagecolordeallocate($this->color, 1);
                imagedestroy($this->img);
            }
            SgvCache::set($file,$this->steamid.'_last', 30);
        }
        header( "Content-type: image/png" );
        echo file_get_contents($file);
    }
    
    private function render_forms($img) {
        imagefilledrectangle($img, 5, 5, 55, 55, $this->color);
    }
    
    private function set_status() {
        if (isset($this->user->gameid)) {
            $this->img = @ImageCreateFromString($this->get_game_image());
            $this->set_ingame($this->img);
        } else {
            $status = $this->user->personastate;
            switch ($status) {
                default:
                    $this->set_offline($this->img);
                    break;
                case 1: case 2: case 3:
                    $this->set_online($this->img);
                break;
            }
        }
    }
    
    private function set_online($img) {
        $this->color = imagecolorallocate( $img, 123, 175, 214 );
        imagettftext ( $img , 10 , 0 , 60 , 33 , $this->color , $this->font, 'Zurzeit Online' );
    }
    
    private function set_offline($img) {
        $this->color = imagecolorallocate( $img, 112, 108, 107 );
        imagettftext ( $img , 10 , 0 , 60 , 33 , $this->color , $this->font, 'Zurzeit Offline' );
    }
    
    private function set_ingame($img) {
        $this->color = imagecolorallocate( $img, 155, 200, 97 );
        imagettftext ( $img , 10 , 0 , 60 , 33 , $this->color , $this->font, 'Zurzeit im Spiel' );
    }
    
    public function set_avatar($img, $url) {
        $avatar = @ImageCreateFromJPEG($url);
        imagecopyresized($img, $avatar, 8,8,0,0,45,45,64,64);
    }

    private function get_game_image() {
        $appid = $this->user->gameid;
        $file = $this->cache_dir.'game_'.$appid.'.png';
        if (!file_exists($file)) {
            $img = @ImageCreateFromPNG($this->background);
            $game = @ImageCreateFromString($this->user->get_game_image($this->user->gameid));
            imagecopyresized($img, $game, 140,0,0,0,160,60,184,69);
            $game = @ImageCreateFromPNG('over_game.png');
            imagecopyresized($img, $game, 140,0,0,0,160,60,160,60);
            imagepng($img,$file);
            imagedestroy($img);
        }
        return file_get_contents($file);
    }
    
    public function set_username($img, $username) {
        imagettftext ( $img , 12 , 0 , 60 , 20 , $this->color , $this->font, $username );
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
        return file_get_contents("http://media.steampowered.com/steamcommunity/public/images/apps/$appid/".$this-> get_game_hash($appid).".jpg");
    }

    function load_last_games($steamid) {
        $this->games = ($this->games === NULL) ? $this->getJson(
            "http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=90245BB467E201DE99CF36C6FD1ED9FA&steamid=$steamid"
        ) : $this->games;
    }

    function get_steam_player_informations($steamid) {
        $player_stats = SgvCache::get($this->steamid.'_stats', 180);
        if ($player_stats === NULL) {
            $player_stats = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=90245BB467E201DE99CF36C6FD1ED9FA&steamids=".$steamid);
            SgvCache::set($player_stats,$this->steamid.'_stats');
        }
        return json_decode($player_stats);
    }

    function getJson($url) {
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 20,
            )
        ));

        if ($ret = @file_get_contents($url, NULL, $ctx)){
            if (strpos($http_response_header[0], "200")) {
                return json_decode($ret);
            } else {
                $this->server_overloaded = true;
            }
        } else {
            $this->server_overloaded = true;
        }
        if ($this->server_overloaded) die('Steam Server unaviable');
        return false;
    }
}