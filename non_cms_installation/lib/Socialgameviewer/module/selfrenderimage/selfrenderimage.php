<?php
function render($player, $settings) {
    return '<img src="'.dirname($_SERVER['PHP_SELF']).'/lib/Socialgameviewer/module/selfrenderimage/steam.user.php?comid='.$player['comid'].'" width="'.$settings->x_size.'" height="'.$settings->x_size*0.2.'"/>';
}