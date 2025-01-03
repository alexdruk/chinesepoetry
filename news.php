<?php
require_once __DIR__.'/globals.php';
$template_info["page_description"] = 'Новости портала "Китайская поэзия"';
$template_info["title"] ='Новости';
$records = array();
$max = getMaxIDFromNews();
if (array_key_exists('from', $_GET)) {
    $from = $_GET['from'];
    $template_info["canonical"] = "https://chinese-poetry.ru/news.php?from=" . $from;
}
else {
    $from = $max; //not defined max
}
$records = getAllNews($from); 

$ids = [];
for ($i = 0; $i < count($records); $i++)  {
    array_push($ids, $records[$i][0]);
}
$from = min($ids)-1;
$template_info["from"] = $from;
$template_info["records"] = $records;
$template = $twig->load('news.html.twig');
echo $template->display($template_info);
