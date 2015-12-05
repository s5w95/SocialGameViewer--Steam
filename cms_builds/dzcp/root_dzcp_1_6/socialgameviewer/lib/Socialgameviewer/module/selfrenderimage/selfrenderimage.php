<?php
function render($player, $settings) {
    return '<a href="'.$player['profile_url'].'">'
                .'<img src="'.dirname($_SERVER['PHP_SELF']).'/render/'.$player['comid'].'" width="'.$settings->x_size.'" height="'.$settings->x_size*0.2.'" title="Profile from '.$player['comid'].'"/>'
            .'</a>';
}