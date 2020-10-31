<?php
require_once __DIR__.'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Свыше 3000 стихов 429 авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Поэзия Китая';
$records = getAllNews();
$reduced = array_slice($records, 0, 5);
$template_info["records"] = $reduced;
$template = $twig->load('home.html.twig');
echo $template->display($template_info);
?>
