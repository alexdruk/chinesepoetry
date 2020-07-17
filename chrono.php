<?php
require_once __DIR__.'/globals.php';
$template_info["title"] = 'Xронология Китая';
$template_info["header"] ='Краткая хронология Китая';
$template = $twig->load('chrono.html.twig');
echo $template->display($template_info);
