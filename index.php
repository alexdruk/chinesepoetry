<?php
require_once __DIR__.'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Свыше 3000 стихов 429 авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Поэзия Китая';
$template_info["content"] ='Chinese poetry web portal project';

$template = $twig->load('page_root.html.twig');
echo $template->display($template_info);
?>
