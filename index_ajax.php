<?php
$index = isset($_GET['index']) ? $_GET['index'] : 'standalone';
$ajax = file_get_contents('lib/Socialgameviewer/_ajax/loading.html');
$ajax = str_replace('{index}', 'index_'.$index.'.php', $ajax);
echo $ajax;