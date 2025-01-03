<?php
require_once __DIR__.'/globals.php';
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Стихи сотен авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Переводчики';
$records = array();
if ($_GET['action'] == 'showall') {
    $template_info["header"] ='Все переводчики';
    $template_info["showall"] = true;
    $template_info["byTranslator"] = false;
    $template_info["search"] = false;
    $records = getAllfromTranslators();
	$template_info["records"] = $records;
    $template_info["title"] ='Все переводчики';
    $template = $twig->load('translators_showall.html.twig');
}

elseif ($_GET['action'] == 'search') {
    $template_info["header"] ='Поиск по именам и биографиям переводчиков';
    if (array_key_exists('posted', $_GET)) {
        $template_info["byTranslator"] = false;
        $template_info["showall"] = true;
        $template_info["search"] = false;
        $pattern = $_POST['pattern']; 
        $records = searchTranslators($pattern);
        $template_info["records"] = $records;
        if (count($records) < 1) {
            $template_info["header"] ='Ничего не найдено';
        }
    }
    else {
        $template_info["showall"] = false;
        $template_info["search"] = true;
    }
    $template_info["title"] ='Поиск по именам и биографиям переводчиков';
    $template = $twig->load('translators_showall.html.twig');
}
elseif ( ($_GET['action'] == 'show') && ($_GET['record_id'] > 0) ){
    $translator_id = $_GET['record_id'];
    $template_info["canonical"] = "https://chinese-poetry.ru/translators.php?action=show&record_id=" . $translator_id;
    list($id, $translator_id,$full_name,$dates,$summary,$img,$doc_text) = getTranslatorDescByID($translator_id);
    list($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present) = getByIDFromTranslators($translator_id);
    $template_info["present"] = false;
    if ($present) {
        $template_info["present"] = true;
    }
    $template_info["header"] = $full_name;
    $template_info["dates"] = $dates;
    $template_info["summary"] = $summary;
    $template_info["img"] = $img;
    $template_info["translator_id"] = $translator_id;
    if (strlen($doc_text) < 200) {
        $doc_text = '<h4>Пожалуйста, помогите собрать информацию для этой страницы!</h4>';
    }
    $template_info["doc_text"] = $doc_text;
    $template_info["title"] = $full_name.' - краткая биография';
    $template_info["page_description"] = $full_name.' ('.$dates.'). '.$summary;
    $template = $twig->load('translators_desc.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page_root.html.twig');
}
echo $template->display($template_info);