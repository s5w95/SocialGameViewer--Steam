<?php
## OUTPUT BUFFER START ##
include("../inc/buffer.php");
## INCLUDES ##
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
## SETTINGS ##
$time_start = generatetime();
lang($language);
$where = "Installer";
$title = $pagetitle." - ".$where."";
$version = "1.4";
## INSTALLER ##
if(isset($_POST['submit'])) {
		
		db("DROP TABLE IF EXISTS ".$sql_prefix."socialgameviewer_settings");		
		db("DROP TABLE IF EXISTS ".$sql_prefix."socialgameviewer_users");			
				
		db("CREATE TABLE ".$sql_prefix."socialgameviewer_settings (
					`id` int(1) NOT NULL auto_increment,
					`max_disp` int(10),
					`x_size` int(20),
					`cache_delay` int(20),
					`version` varchar(5),
					`spalte` varchar(10),
					`table` varchar(10),
					`view_vac` int(1),
					`view_privat` int(1),
					`view_steamlink` int(1),
					`view_offline` int(1),
					`view_addfriend` int(1),
					`view_newtab` int(1),
					`steam_api_key` varchar(32),
					PRIMARY KEY  (`id`))"); 
					
		db("CREATE TABLE ".$sql_prefix."socialgameviewer_users (
					`id` int(1) NOT NULL auto_increment,
					`steamid` varchar(18),
					`comid` varchar(18),
					`userid` int(5),
					PRIMARY KEY  (`id`))"); 
					
		db(" INSERT INTO `".$sql_prefix."socialgameviewer_settings` (
		`id` ,
		`max_disp` ,
		`x_size` ,
		`cache_delay` ,
		`version` ,
		`spalte` ,
		`table`,
		`view_vac`,
		`view_privat`,
		`view_steamlink`,
		`view_offline`,
		`view_addfriend`,
		`view_newtab`,
		`steam_api_key`
		)
		VALUES (
		NULL , '20', '180', '300', '".$version."', 'steamid', 'users', '1', '1', '1', '0', '0', '1','90245BB467E201DE99CF36C6FD1ED9FA'
		);");
							
	if ($fehler == 0) {
    $show = '<tr>
               <td class="contentHead" align="center"><span class="fontGreen"><b>Installation erfolgreich!</b></span></td>
             </tr>
             <tr>
               <td class="contentMainFirst"  align="center">
                 Die ben&ouml;tigten Tabellen konnten erfolgreich erstellt werden.<br>
                 <br>
                 <b>L&ouml;sche unbedingt den installer-Ordner!</b>
               </td>
             </tr>
             <tr>
               <td class="contentBottom"></td>
             </tr>';
  } else {
    $show = '<tr>
               <td class="contentHead" align="center"><span class="fontWichtig"><b>FEHLER</b></span></td>
             </tr>
             <tr>
               <td class="contentMainFirst" align="center">
                 Bei der Installation des Addons ist ein Fehler aufgetreten. Bitte &uuml;berpr&uuml;fe deine Datenbank auf Sch&auml;den und versuche die Installation erneut.
               </td>
             </tr>
             <tr>
               <td class="contentBottom"></td>
             </tr>';
  }
} else {
  $show = '<tr>
             <td class="contentHead" align="center"><b>SocialGameViewer Addon - Installation</b></td>
           </tr>
           <tr>
             <td class="contentMainFirst" align="center">
               Hallo und herzlichen Dank, dass du dieses Addon für das deV!L’z Clanportal von <a href="http://www.HD-Gamers.de" target="_blank">www.HD-Gamers.de</a>
               heruntergeladen hast. Dieser Installer soll dir die Arbeit abnehmen, die ben&ouml;tigten Tabellen in der Datenbank manuell erstellen zu m&uuml;ssen.<b>
               <br /><br />
               <b><span style="text-align:center"><u>!!!! WICHTIG !!!!</u></span><br />Erstell vor dem ausf&uuml;hren des Installers ein Datenbank BackUp. Wir haften f&uuml;r keine Sch&auml;den!</b><br />
               <br />
             </td>
           </tr>
           <tr>
             <td class="contentBottom" align="center">
               <form action="?action=install" method="POST">
                 <input class="submit" type="submit" name="submit" value="Tabellen anlegen">
               </form>
             </td>
           </tr>';
}
## SETTINGS ##
$time_end = generatetime();
$time = round($time_end - $time_start,4);
page($show, $title, $where,$time);
?>
