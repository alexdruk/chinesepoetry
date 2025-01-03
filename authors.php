<?php
require_once __DIR__.'/globals.php';
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Стихи сотен авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Авторы';
$records = array();
if ($_GET['action'] == 'showall') {
    $template_info["header"] ='Все авторы';
    $template_info["showall"] = true;
    $template_info["show_alphabet"] = true;
    $template_info["search"] = false;
    $template_info["byAuthor"] = false;
    $records = getAllfromAuthors();
	$template_info["records"] = $records;
    $template_info["title"] ='Все авторы';
    $template = $twig->load('authors_showall.html.twig');
}
elseif ($_GET['action'] == 'byEpoch') {
    $template_info["header"] ='По эпохам';
    $template_info["showall"] = true;
    $template_info["show_alphabet"] = false;
    $template_info["search"] = false;
    if (array_key_exists('posted', $_GET)) {
        $epoch = $_GET['epoch']; 
        $records = getAllfromAuthorsByEpoch($epoch);
        $template_info["records"] = $records;
        $template_info["title"] ='Все авторы по эпохам';
        $template_info["page_description"] = 'Список всех авторов эпохи "'.$epoch.'".';
        $template = $twig->load('authors_showall.html.twig');
    }
    else {
        $template_info["byAuthor"] = false;
        $template = $twig->load('authors_byEpoch.html.twig');
    }
} elseif ($_GET['action'] == 'sex') {
    $template_info["header"] = 'Поэтессы';
    $template_info["showall"] = true;
    $template_info["show_alphabet"] = false;
    $template_info["search"] = false;
    $records = getAllfromAuthorsBySex();
    $template_info["records"] = $records;
    $template = $twig->load('authors_showall.html.twig');
    $template_info["title"] = 'Все поэтессы';
    $template_info["page_description"] = 'Список всех поэтесс';
}
elseif ($_GET['action'] == 'search') {
    $template_info["header"] ='Поиск по именам авторов';
    if (array_key_exists('posted', $_GET)) {
        $template_info["showall"] = true;
        $template_info["show_alphabet"] = false;
        $template_info["search"] = false;
        $template_info["byAuthor"] = false;
        $pattern = $_POST['pattern']; 
        $records = searchAuthors($pattern);
	    $template_info["records"] = $records;
        if (count($records) < 1) {
            $template_info["header"] ='Ничего не найдено';
        }
    }
    else {
        $template_info["showall"] = false;
        $template_info["search"] = true;
        $template_info["byAuthor"] = false;
    }
    $template_info["title"] ='Поиск по именам авторов';
    $template = $twig->load('authors_showall.html.twig');
}
elseif ( ($_GET['action'] == 'show') && ($_GET['record_id'] > 0) ){
    $record_id = $_GET['record_id'];
    $template_info["canonical"] = "https://chinese-poetry.ru/authors.php?action=show&record_id=" . $record_id;
    list($proper_name, $dates, $epoch, $doc_text) = getDocByIDFromAuthors($record_id);
    $template_info["epoch"] = '<span class="epoch">'.$epoch.'</span>';
    $template_info["author_id"] = $record_id;
    $atribs = getAtributesByAuthorID($record_id);
    $template_info["title"] = $template_info["title"].' | '.$proper_name;
    if(count(array_filter($atribs)) < 4) { #consider id, author_id and forseach
        $template_info["atribs"] = false;
    }
    else {
        if (($atribs[2]) || ($atribs[3]) || ($atribs[4]) || ($atribs[5])) {
            $template_info["name"] = true;
        }
        else {
            $template_info["name"] = false;
        }
        if (($atribs[6]) || ($atribs[7]) || ($atribs[8]) || ($atribs[9])) {
            $template_info["real_name"] = true;
        }
        else {
            $template_info["real_name"] = false;
        }
        if (($atribs[10]) || ($atribs[11]) || ($atribs[12]) || ($atribs[13])) {
            $template_info["s_name"] = true;
        }
        else {
            $template_info["s_name"] = false;
        }
        if (($atribs[14]) || ($atribs[15]) || ($atribs[16]) || ($atribs[17])) {
            $template_info["p_name"] = true;
        }
        else {
            $template_info["p_name"] = false;
        }
        if (($atribs[18]) || ($atribs[19]) || ($atribs[20]) || ($atribs[21])) {
            $template_info["pseudo"] = true;
        }
        else {
            $template_info["pseudo"] = false;
        }
        if (($atribs[22]) || ($atribs[23]) || ($atribs[24]) || ($atribs[25])) {
            $template_info["nick"] = true;
        }
        else {
            $template_info["nick"] = false;
        }
        $template_info["atribs"] = $atribs;
    }
    $zname = '';
    if ($atribs[3]) {
        $zname = '&nbsp;<span class="name zh">'.$atribs[3].'</span>';
    }
    else if ($atribs[4]) {
        $zname = '&nbsp;<span class="name zh">'.$atribs[4].'</span>';
    }
    $template_info["header"] = '<span class="author name">'.$proper_name.'</span> <span class="author dates">'.$dates.'</span>'.$zname;
 
    if (strlen($doc_text) < 200) {
        $doc_text = '<h4>Пожалуйста, помогите собрать информацию для этой страницы!</h4>';
    }
    $template_info["doc_text"] = '<span class="description">'.$doc_text.'</span>';
    $template_info["title"] = $proper_name.' '.$dates.' - биография';
    $atribs = array_slice($atribs, 2);
    array_pop($atribs);
    $atribs = array_unique($atribs);
    array_shift($atribs);
    $atribs = array_map('trim', $atribs);
    $all_other_names = join(', ', $atribs);
    $template_info["page_description"] = $proper_name.' '.$dates.', '.$epoch.', также извесный как '.$all_other_names.'. Биографические материалы.';
    $template = $twig->load('description.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page_root.html.twig');
}
echo $template->display($template_info);