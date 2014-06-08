<?php
require_once('lib/Socialgameviewer/sgv_base.php');

sgv_set_language('de'); //Sprache wird dem output Modul übergeben wird jedoch nicht von allen unterstützt
sgv_set_steam_api_key('90245BB467E201DE99CF36C6FD1ED9FA'); //Steam API Key wird benötigt um Status und sonstige Informationen der User Abzurufen
                                                           //Es sind maximal 100.000 Abfragen pro tag möglich
sgv_caching_size('high'); //low: no Game Image Rendering
                          //medium: only in Game Images with Game Image
                          //high: last/activ game status on every output;
sgv_wipe_interval('weekly');
//Bestimmte Anforderungen die ein Profil erfüllen muss um angezeigt zu werden;
sgv_set_cache_delay(300);
sgv_show_offline(false);
sgv_show_addfriend(true); //Abfrage ob ein AddFriend Button angezeigt werden soll dies muss auch vom Output Modul unterstützt werden
sgv_show_newtab(true);
sgv_set_max_disp(300); //Maximale Ausgaben
sgv_set_output_modul('selfrenderimage');
sgv_set_input_modul('steam_group');
sgv_set_group('hd-gamers-de');
sgv_show_private(true);
sgv_set_x_size(200);

$sg = new Sgv_steam_group($sgv);
$sg->render_viewer();