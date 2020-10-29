<?php
require_once __DIR__.'/globals.php';
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Свыше 3000 стихов 429 авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Темы';
$records = array();

if ($_GET['action'] == 'show') {
    if (array_key_exists('record_id', $_GET)) {
        $template_info["showall"] = false;
        $template_info["desc"] = true;
        $template_info["search"] = false;
        $record_id = $_GET['record_id'];
        list($topics_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($record_id);
        if (!$topic_synonym) {
            $header = $topic_name;
        }
        else {
            $header = $topic_name.' <span class ="topic_synonym">('.$topic_synonym.')</span)';
        }
        $template_info["header"] = $header;
        $template_info["title"] = $template_info["title"].':  "'.$topic_name.'"';
        $template_info["page_description"] = 'Описание темы "'.$topic_name.'"';
        $template_info["topic_desc"] = $topic_desc;
        $template_info["topic_id"] = $topics_id;
    }
    else {
        $template_info["showall"] = true;
        $template_info["desc"] = false;
        $template_info["search"] = false;
        $template_info["header"] = 'Темы';
        $records = getAllFromTopics();
        $template_info["records"] = $records;
    }
    $template = $twig->load('topics_showall.html.twig');    
}
elseif ($_GET['action'] == 'search') {
    $template_info["desc"] = false;
    if (array_key_exists('posted', $_GET)) {
        $template_info["showall"] = true;
        $template_info["search"] = false;
        $pattern = $_POST['pattern'];
        $template_info["header"] = 'Результат поиска';
        $records = searchTopics($pattern);
        $template_info["records"] = $records;
        if (count($records) < 1) {
            $template_info["header"] ='Ничего не найдено';
        }
    }
    else {
        $template_info["search"] = true;
        $template_info["showall"] = false;
    }
    $template_info["title"] = 'Полнотекстовый поиск по темам';
    $template = $twig->load('topics_showall.html.twig');    
}
echo $template->display($template_info);
