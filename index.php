<?php
$index = isset($_GET['index']) ? $_GET['index'] : 'standalone';
header('Location: '.'index_'.$index.'.php');