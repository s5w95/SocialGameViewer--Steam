<?php
if(_adminMenu != 'true') exit;

	if (version_compare(PHP_VERSION, '5.4.0') < 0) {
		die('php version outdated');
	}

    $where = $where.': '._config_socialgameviewer;
    if($chkMe != 4)
    {
      $show = error(_error_wrong_permissions, 1);
    } else {
    
    $this_db = $sql_prefix.'socialgameviewer_settings';
    $qry = db("SELECT * FROM ".$this_db);
    $get = _fetch($qry);
    
	$out = "";
	$in = "";
	if ($handle = opendir('../socialgameviewer/lib/Socialgameviewer/modul_info')) {
		while (false !== ($file = readdir($handle))) {
			$file = explode('.',basename($file))[0];
			if (strpos($file, 'out_')!==false) {
				$file = str_replace('out_','',$file);
				$selected = ($file == $get['output_modul']) ? 'selected' : '';
				$out .= '<option value="'.$file.'" '.$selected.'>'.$file.'</option>';
			} else if (strpos($file, 'in_')!==false) {
				$file = str_replace('in_','',$file);
				$selected = ($file == $get['input_modul']) ? 'selected' : '';
			    $in .= '<option value="'.$file.'" '.$selected.'>'.$file.'</option>';
			}
		}
		closedir($handle);
	}
	
    $show_ = show($dir."/form_socialgameviewer", array(
        "max_disp" => re($get['max_disp']),
        "x_size" => re($get['x_size']),
        "view_privat" => re($get['view_privat'] == 1 ? ' checked="checked"' : ''),
        "view_offline" => re($get['view_offline'] == 1 ? ' checked="checked"' : ''),
        "view_addfriend" => re($get['view_addfriend'] == 1 ? ' checked="checked"' : ''),
        "view_steamlink" => re($get['view_steamlink'] == 1 ? ' checked="checked"' : ''),
        "view_newtab" => re($get['view_newtab'] == 1 ? ' checked="checked"' : ''),												 
        "cache_delay" => re($get['cache_delay']),
		"steam_group" => re($get['group']),
        "steam_api_key" => re($get['steam_api_key']),
        "value" => _button_value_edit,
		"options_input_modules" => $in,
		"options_output_modules" => $out,
        "what" => 'socialgameviewer'
        ));
	 
    $status = @simplexml_load_file("http://hd-gamers.de/addons/socialgameviewer/version.xml");
    if ($status->version > $get['version']) {
        $version = '<font color="#FE2E2E">'.$get['version'].'</font> - <a href="'.$status->download.'">Update Downloaden</a>';
    } else {
        $version = '<font color="#3ADF00">'.$get['version'].'</font>';
    }
    $version .= '<font color="#BDBDBD"> | Updatecheck by HD-Gamers.de</font>';
    $show = show($dir."/socialgameviewer", array(
        "version" => $version,
        "head" => _config_socialgameviewer,
        "what" => "socialgameviewer",
        "show" => $show_
	));
													
    if($_GET['do'] == "update")
    {
      $qry = db("UPDATE ".$this_db."
                 SET `max_disp` = '".up($_POST['max_disp'])."',
                `x_size` = '".up($_POST['x_size'])."',
                `view_privat` = '".up($_POST['view_privat'])."',
                `view_offline` = '".up($_POST['view_offline'])."',
                `view_addfriend` = '".up($_POST['view_addfriend'])."',
                `view_steamlink` = '".up($_POST['view_steamlink'])."',
                `cache_delay` = '".up($_POST['cache_delay'])."',
                `steam_api_key` = '".up($_POST['steam_api_key'])."',
                `view_newtab` = '".up($_POST['view_newtab'])."',
                `group` = '".up($_POST['steam_group'])."',
                `input_modul` = '".up($_POST['input_modul'])."',
                `output_modul` = '".up($_POST['output_modul'])."' 
                 WHERE id = 1");
      $show = info(_config_set, "?admin=socialgameviewer");
	  full_wipe();
    }
}

function full_wipe() {
	$files = glob('../socialgameviewer/lib/Socialgameviewer/_local.cache/*');
	foreach($files as $file) {
		if(is_file($file))
		unlink($file);
	}
}
