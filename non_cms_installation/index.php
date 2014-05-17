<?php

require_once('lib/Socialgameviewer/Sgv.php');

sgv_set_language('de'); //Sprache wird dem output Modul übergeben wird jedoch nicht von allen unterstützt
sgv_enable_phpfastcache(false); //falls PHPfastCache vorhanden ist kann dies hier aktiviert werden ebenso der Pfad zur Lib

sgv_set_steam_api_key('90245BB467E201DE99CF36C6FD1ED9FA'); //Steam API Key wird benätigt um Status und sonstige Informationen der User Abzurufen
														   //Es sind maximal 100.000 Abfragen pro tag möglich

//Bestimmte Anforderungen die ein Profil erfüllen muss um angezeigt zu werden;
sgv_show_offline(false);
sgv_show_addfriend(true); //Abfrage ob ein AddFriend Button angezeigt werden soll dies muss auch vom Output Modul unterstützt werden
sgv_show_newtab(true);
sgv_set_max_disp(20); //Maximale Ausgaben

$sg = new Sgv_steam_group($sgv);
$sg->render_viewer();