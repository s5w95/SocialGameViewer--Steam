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

   global $db, $userid, $llreg;

		$settings =  mysql_fetch_object( db('SELECT * FROM '.$db['socialgameviewer_settings'].' WHERE id = 1'));
		
        $qry_steam = db ( 'SELECT id,steamid,level' . ' FROM ' . $db[ $settings->table ] . ' WHERE ' . $settings->spalte . ' != "" AND level > 2 ORDER BY nick;' );
		$cache = basePath.'/__cache/'.md5(socialgameviewer).'.cache';

		if (time() - filemtime($cache) > $settings->cache_delay)
		{		$count = 0;	
				$handle = fopen($cache, "w");
				while ( $get = _fetch ( $qry_steam ))				 
				{	
					if ($count < $settings->max_disp)
					{

						$ret = "";
						$data = $get['steamid'];
						$data=strtolower(trim($data));
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
								$steam_profile = simplexml_load_file("http://steamcommunity.com/profiles/".$ret."/?xml=1");
							}
							else
							{
								$steam_profile = simplexml_load_file("http://steamcommunity.com/id/".str_replace('steam_','ERROR_POFILE_FIXED',$data)."/?xml=1");
								$ret = $steam_profile->steamID64;
							}
							$state = $steam_profile->onlineState;						
							if ($state != "offline" && empty($steam_profile->error) && $ret != "") 
							{
								$output .= '<a href="../user/?action=user&id='.$get['id'].'"><img src=http://steamsignature.com/status/english/'.$ret.'.png width='.$settings->x_size.'px ></a>';
								$count++;
							}
						}
					}				
									
				}				
				if ($count == 0) $output = "Keine Member Online";
				fwrite($handle, $output); 				
				fclose($handle);
		
		} else $output = file_get_contents($cache);
	echo $output;
?>