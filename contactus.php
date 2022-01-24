<?php
require_once __DIR__ . '/globals.php';
$template_info["page_description"] = 'Контакт';
$template_info["title"] = 'Пишите нам';
$template = $twig->load('email.html.twig');
echo $template->display($template_info);
