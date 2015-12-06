<?php

//Config
define('STEAM_API_REQUEST_DELAY', 300); //Seconds
define('STEAM_API_KEY', '90245BB467E201DE99CF36C6FD1ED9FA');

include('../../sgv_base.php');

class SteamImage
{

    private $user;
    private $background = 'steambak.png';
    private $cache_dir;
    private $color;
    private $img;
    private $font = 'arial';
    private $steamid;

    private $main_refresh_rate;

    public function __construct($steamid)
    {
        $this->main_refresh_rate = STEAM_API_REQUEST_DELAY;
        $this->cache_dir = SGV_BASE . '_local.cache/';
        $this->steamid = $steamid;
        $this->font = './' . $this->font . '.ttf';
    }

    public function render()
    {
        $file = SgvCache::get($this->steamid . '_last', $this->main_refresh_rate);
        $this->user = new SteamUser($this->steamid);
        if ($file === NULL | $this->user->load_steam_player_information($this->main_refresh_rate)) {
            $this->img = @ImageCreateFromPNG($this->background);
            $status = $this->user->personastate > 0 ? 1 : 0;
            if (isset($this->user->gameid)) $status = '2_' . $this->user->gameid;
            $file = $this->cache_dir . $this->steamid . '_' . $status . '.png';

            if (!file_exists($file)) {
                $this->set_status();
                $this->render_forms($this->img);
                $this->set_avatar($this->img, $this->user->avatarmedium);
                $this->set_username($this->img, $this->user->personaname);
                imagepng($this->img, $file);
                imagedestroy($this->img);
            }
            SgvCache::set($file, $this->steamid . '_last');
        }
        header("Content-type: image/png");
        echo file_get_contents($file);
    }

    private function render_forms($img)
    {
        imagefilledrectangle($img, 5, 5, 55, 55, $this->color);
    }

    private function set_status()
    {
        if (isset($this->user->gameid)) {
            $this->img = @ImageCreateFromString($this->get_game_rendered_image());
            $this->set_ingame($this->img);
        } else {
            $status = $this->user->personastate;
            switch ($status) {
                default:
                    $this->set_offline($this->img);
                    break;
                case 1:
                case 2:
                case 3:
                    $this->set_online($this->img);
                    break;
            }
        }
    }

    private function set_online($img)
    {
        $this->color = imagecolorallocate($img, 123, 175, 214);
        imagettftext($img, 10, 0, 60, 33, $this->color, $this->font, 'Zurzeit Online');
    }

    private function set_offline($img)
    {
        $this->color = imagecolorallocate($img, 112, 108, 107);
        imagettftext($img, 10, 0, 60, 33, $this->color, $this->font, 'Zurzeit Offline');
    }

    private function set_ingame($img)
    {
        $this->color = imagecolorallocate($img, 155, 200, 97);
        imagettftext($img, 10, 0, 60, 33, $this->color, $this->font, 'Zurzeit im Spiel');
    }

    public function set_avatar($img, $url)
    {
        $avatar = @ImageCreateFromJPEG($url);
        imagecopyresized($img, $avatar, 8, 8, 0, 0, 45, 45, 64, 64);
    }

    private function get_game_rendered_image()
    {
        $appid = $this->user->gameid;
        $file = $this->cache_dir . 'game_' . $appid . '.png';
        if (!file_exists($file)) {
            $img = @ImageCreateFromPNG($this->background);
            $game = @ImageCreateFromString($this->user->get_game_image($this->user->gameid));
            imagecopyresized($img, $game, 140, 0, 0, 0, 160, 60, 184, 69);
            $game = @ImageCreateFromPNG('over_game.png');
            imagecopyresized($img, $game, 140, 0, 0, 0, 160, 60, 160, 60);
            imagepng($img, $file);
            imagedestroy($img);
        }
        return file_get_contents($file);
    }

    public function set_username($img, $username)
    {
        imagettftext($img, 12, 0, 60, 20, $this->color, $this->font, $username);
    }
}

class SteamUser
{

    public $steamid;
    private $games;
    public $personaname,
        $avatarmedium,
        $gameid,
        $personastate;

    public function __construct($steamid)
    {
        $this->steamid = $steamid;
    }

    private function get_game_hash($appid)
    {
        $this->load_last_games();
        $games = $this->games->response->games;
        foreach ($games as $game) {
            if ($game->appid == $appid) {
                return $game->img_logo_url;
            }
        }
        return false;
    }

    function get_game_image($appid)
    {
        return file_get_contents("http://media.steampowered.com/steamcommunity/public/images/apps/$appid/" . $this->get_game_hash($appid) . ".jpg");
    }

    private function load_last_games()
    {
        $this->games = json_decode($this->file_get_content_merged(
            "http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key=" . STEAM_API_KEY . "&steamid=" . $this->steamid
        ));
    }


    public function load_steam_player_information($rate)
    {
        $old = SgvCache::get($this->steamid . '_stats');

        if ($old === NULL || time() - SgvCache::get_filemtime($this->steamid . '_stats') > $rate) {
            $new = $this->file_get_content_merged("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . STEAM_API_KEY . "&steamids=" . $this->steamid);
            SgvCache::set($new, $this->steamid . '_stats');
        } else {
            $new = $old;
        }

        $user = json_decode($new)->response->players[0];
        foreach ($user as $key => $value) {
            $this->$key = $value;
        }
        if ($old == $new) {
            return false;
        } else {
            return true;
        }
    }

    private function file_get_content_merged($url)
    {
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => 20,
            )
        ));
        if ($ret = @file_get_contents($url, NULL, $ctx)) {
            if (strpos($http_response_header[0], "200")) {
                return $ret;
            } else {
                $server_overloaded = true;
            }
        } else {
            $server_overloaded = true;
        }
        if ($server_overloaded) die('Steam Server unavailable');
        return false;
    }
}

$image = new SteamImage($_GET['comid']);
$image->render();