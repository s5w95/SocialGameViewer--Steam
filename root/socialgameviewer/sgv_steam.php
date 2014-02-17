<?	
# SocialGameViewer by Tune389
## OUTPUT BUFFER START ##
include("../inc/buffer.php");
## INCLUDES ##
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
## SETTINGS ##
lang($language);
$dir = "socialgameviewer"; 

   global $db;

		$settings =  mysqli_fetch_object( db('SELECT * FROM '.$db['socialgameviewer_settings'].' WHERE id = 1 LIMIT 1'));
		if ($settings->view_addfriend == 1) {$x_size = $settings->x_size*0.88; $width = $settings->x_size*0.12; $add = '<img src="http://steamsignature.com/AddFriend.png" width="'.$width.'"/>';}
		else $x_size = $settings->x_size;
        $qry_steam = db ( 'SELECT t1.id,t1.steamid FROM '.$db['users'].' t1 WHERE t1.steamid NOT LIKE "" AND t1.steamid NOT IN (SELECT steamid FROM '.$db['socialgameviewer_users'].');' );
		$cache = basePath.'/__cache/'.md5(socialgameviewer_1_4).'.cache';
		
		if (time() - filemtime($cache) > $settings->cache_delay)
		{	
			$online_member = 0;	
			$update_counter = 0;
			$handle = fopen($cache, "w");
			while ( $get = _fetch ( $qry_steam ))	
			{
				$data="";$ret="";
				$data=strtolower(trim($get['steamid']));
				if ($data!='') 
				{
					if (ereg('7656119', $data))
					{
						$ret = $data;
					}
					else if (substr($data,0,7)=='steam_0') 
					{
						$tmp=explode(':',$data);
						if ((count($tmp)==3) && is_numeric($tmp[1]) && is_numeric($tmp[2]))
						{							
							$friendid=($tmp[2]*2)+$tmp[1]+1197960265728;
							$friendid='7656'.$friendid;
							$ret = $friendid;
						}
					}
					if ($ret!="")
					{
						$comid = $ret;
					}
					else
					{
						$steam_profile = simplexml_load_file("http://steamcommunity.com/id/".str_replace('steam_','ERROR_POFILE_FIXED',$data)."/?xml=1");
						$comid = $steam_profile->steamID64;
					}
				}
				db ("INSERT INTO ".$db['socialgameviewer_users']." (`id`, `steamid`, `comid`, `userid`) VALUES (NULL, '".$get['steamid']."', '".$comid."', '".$get['id']."');");
				$update_counter++;
			}

			if ($update_counter != 0) $output = $update_counter." - Profiles Updated!<br/><hr>";
			$qry_steam = db ( 'SELECT t1.comid, t1.steamid, t1.userid, t1.id FROM '.$db['socialgameviewer_users'].' t1 INNER JOIN '.$db['users'].' t2 ON (t1.steamid = t2.steamid)' );
			$count = 0;
			while ( $get = _fetch ( $qry_steam ))	
			{
				if ($get['comid'] != 0) 
				{
					$playerid[(string)$get['comid']] = $get['userid'];
					$players .= $get['comid'].",";$count++;
				}
			}
			$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$settings->steam_api_key."&steamids=".substr($players,0,-1);		
			$result = json_decode(file_get_contents($url));
			for ($i=0;$i<$count;$i++)
			{				
				$steamid = $result->response->players[$i]->steamid;
				if ($settings->view_steamlink) $href = 'http://steamcommunity.com/profiles/'.$steamid;						
				else $href = '../user/?action=user&amp;id='.$playerid[(string)$steamid];
				if (!$settings->view_offline && !$result->response->players[$i]->personastate) {}
				//else if (!$settings->view_vac && $result->response->players[$i]) {}
				else if (!$settings->view_privat && $result->response->players[$i]->communityvisibilitystate < 2) {}
				else if (!empty($result->response->players[$i])) 
				{
					if ($settings->view_newtab) $add2 = 'target="_blank"';
					else $add2 = "";
					$output .= '<a href="'.$href.'" '.$add2.'><img src="http://steamsignature.com/status/english/'.$steamid.'.png" width="'.$x_size.'" /></a><a href="steam://friends/add/'.$steamid.'" >'.$add.'</a><br/>' ;
				}
				$online_member++;
			}											
			
			if ($online_member == 0) $output = "Keine Member Online";
			fwrite($handle, $output); 				
			fclose($handle);
			
		} else $output = file_get_contents($cache);
	echo $output;
?>
