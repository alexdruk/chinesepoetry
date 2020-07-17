<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='admin home page';
if (!array_key_exists('page',$_GET)) { //home page//	echo $_GET['action'];
	$template_info["content"] ='This is homepage of Admin site';
	$template = $twig->load('page.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);
