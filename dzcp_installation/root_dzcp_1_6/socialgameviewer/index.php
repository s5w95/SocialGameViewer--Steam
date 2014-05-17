<?php
# SocialGameViewer by Tune389

## OUTPUT BUFFER START ##
include("../inc/buffer.php");

## INCLUDES ##
include(basePath."/inc/debugger.php");
include(basePath."/inc/config.php");
include(basePath."/inc/bbcode.php");
## SETTINGS ##

$dir = "socialgameviewer";

$lang = ($language == 'deutsch') ? 'german' : 'english';

require_once 'Socialgameviewer/Sgv.php';

$sgv = mysqli_fetch_object(db('SELECT * FROM '.$sql_prefix.'socialgameviewer_settings WHERE id = 1 LIMIT 1'));

$sg = new Sgv_steam_group($sgv);
$sg->render_viewer();