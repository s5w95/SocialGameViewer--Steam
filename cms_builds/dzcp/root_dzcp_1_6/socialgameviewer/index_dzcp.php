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

require_once 'lib/Socialgameviewer/sgv_base.php';

$sgv = mysqli_fetch_object(db('SELECT * FROM '.$sql_prefix.'socialgameviewer_settings WHERE id = 1 LIMIT 1'));
sgv_render_viewer();