 <?php
/////////// ADMINNAVI \\\\\\\\\
// Typ:       contentmenu
// Rechte:    $chkMe == 4
///////////////////////////////
if(_adminMenu != 'true') exit;

    $where = $where.': '._config_socialgameviewer;
    if($chkMe != 4)
    {
      $show = error(_error_wrong_permissions, 1);
    } else {
    
	  $this_db = $db['socialgameviewer_settings'];
      $qry = db("SELECT * FROM ".$this_db);
      $get = _fetch($qry);
	  
	

      $show_ = show($dir."/form_socialgameviewer", array(
                                                  "max_disp" => re($get['max_disp']),
												  "x_size" => re($get['x_size']),
												  "table" => re($get['table']),
												  "spalte" => re($get['spalte']),
												  "view_vac" => re($get['view_vac'] == 1 ? ' checked="checked"' : ''),
												  "view_privat" => re($get['view_private'] == 1 ? ' checked="checked"' : ''),
												  "view_offline" => re($get['view_offline'] == 1 ? ' checked="checked"' : ''),
												  "view_addfriend" => re($get['view_addfriend'] == 1 ? ' checked="checked"' : ''),
												  "view_steamlink" => re($get['view_steamlink'] == 1 ? ' checked="checked"' : ''),
												  "cache_delay" => re($get['cache_delay'])
												  ));
	 
	$status = simplexml_load_file("http://hd-gamers.de/addons/socialgameviewer/version.xml");
	if ($status->version > $get['version']) 
	{
		$version = '<font color="#FE2E2E">'.$get['version'].'</font> - <a href="'.$status->download.'">Update Downloaden</a>';
	}
	else 
	{
		$version = '<font color="#3ADF00">'.$get['version'].'</font>';
	}
	$version .= '<font color="#BDBDBD"> | Updatecheck by HD-Gamers.de</font>';
	
      $show = show($dir."/socialgameviewer", array("version" => $version,
													"head" => _config_socialgameviewer,
													"what" => "socialgameviewer",
												    "value" => _button_value_edit,
												    "show" => $show_
													));
													
      if($_GET['do'] == "update")
      {
        $qry = db("UPDATE ".$this_db."
                   SET `max_disp`     = '".up($_POST['max_disp'])."',
                       `x_size`    = '".up($_POST['x_size'])."',
					   `table`    = '".up($_POST['table'])."',
					   `spalte`    = '".up($_POST['spalte'])."',
					   `view_vac`    = '".up($_POST['view_vac'])."',
					   `view_privat`    = '".up($_POST['view_privat'])."',
					   `view_offline`    = '".up($_POST['view_offline'])."',
					   `view_addfriend`    = '".up($_POST['view_addfriend'])."',
					   `view_steamlink`    = '".up($_POST['view_steamlink'])."',
                       `cache_delay`       = '".up($_POST['cache_delay'])."'
                   WHERE id = 1");

        $show = info(_config_set, "?admin=socialgameviewer");
      }
    }
 ?>