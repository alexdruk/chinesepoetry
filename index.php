<?php
require_once __DIR__.'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Стихи сотен авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Поэзия Китая';
$records = getLimitedNewsWithoutFullText();
$template_info["records"] = $records;
$last =  count($records);
$template_info["floor"] = floor(($last)/5);
$template_info["canonical"] = "https://chinese-poetry.ru/";
$template = $twig->load('home.html.twig');
echo $template->display($template_info);
