<?php
require_once __DIR__.'/globals.php';
$pageid = $_REQUEST['pageid'];
$ip = getIP();
$r_id = votes_insert_record($pageid, $ip);
?>
