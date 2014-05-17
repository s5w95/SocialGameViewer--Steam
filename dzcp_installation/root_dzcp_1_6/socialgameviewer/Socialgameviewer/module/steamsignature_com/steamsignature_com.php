<?php
	function render($player, $settings) {

		if ($settings->view_addfriend) {
			$x_size = $settings->x_size*0.88;
			$width = $settings->x_size*0.12;
			$add = '<img src="http://steamsignature.com/AddFriend.png" width="'.$width.'"/>';
		} else {
			$x_size = $settings->x_size;
		}

		return '<img src="http://steamsignature.com/status/english/'.$player['comid'].'.png" width="'.$x_size.'"/>'.$add;
	}