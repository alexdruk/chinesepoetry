<?php
require_once __DIR__.'/globals.php';
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Стихи сотен авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Источники';
if (array_key_exists('action', $_GET)) { 
    $records = array();

    if ($_GET['action'] == 'show_anthology') {
        $template_info["header"] ='Литературные источники упомянутые на данном сайте';
        $records = getFullBiblio(1);       
		$template_info["records"] = $records;
        $template_info["show_alphabet"] = false;
		$template_info["special_message"] = false;
        $template_info["title"] ='Литературные источники';
    }
    elseif ($_GET['action'] == 'needed') {
        $template_info["header"] ='Нам очень нужны';
        $records = getFullBiblio(-1);       
		$template_info["records"] = $records;
        $template_info["show_alphabet"] = false;
		$template_info["special_message"] = 'Если вы знаете где можно достать данные издания, пожалуйста сообщите нам используя форму "Пишите нам" в самом низу страницы.';
    }
     elseif ($_GET['action'] == 'showall') {
        $template_info["header"] ='Литературные источники';
        $records = getFullBiblio(4);       
		$template_info["records"] = $records;
        $template_info["show_alphabet"] = true;
		$template_info["special_message"] = false;
        $template_info["title"] ='Литературные источники';
    }
    elseif ($_GET['action'] == 'search') {
 		if (!$_POST) {
            $template_info["header"] ='Полнотекстовый поиск по источникам';
            $template_info["records"] = false;        
            $template_info["show_alphabet"] = false;
            $template_info["special_message"] = false;
         }
        else {
            if (array_key_exists('pattern', $_POST)) {
                $pattern = (!empty($_POST['pattern'])) ? $_POST['pattern'] : '';
            }
            $records = searchBiblio($pattern);       
            $template_info["records"] = $records;
            $template_info["show_alphabet"] = false;
            $template_info["header"] ='Результаты поиска';
            $template_info["special_message"] = false;
            if (count($records) < 1) {
                $template_info["header"] ='Ничего не найдено';
            }
        }
    }
    $template = $twig->load('biblio.html.twig');
}

elseif ($_GET['biblio_id']) {
    if ( ($_GET['biblio_id']) > 0) {
        $b_id = $_GET['biblio_id'];
        $template_info["header"] ='Литературный источник';
        $records = getFullBiblioByID($b_id);
        // print_r($records);
        $template_info["records"] = $records;
        $template_info["iframe"] = getBiblioIframeByID($b_id);
		$template_info["biblio_id"] = $b_id;
        $template_info["show_alphabet"] = false;
        $template_info["special_message"] = false;
        $template_info["title"] ='Литературный источник';
        $template_info["page_description"] = 'Литературный источник: '.implode($records);
    }
    else {
        $template_info["header"] ='Информация по источнику с данным идентификатором отсутствует';
        $template_info["records"] = false;
        $template_info["show_alphabet"] = false;
        $template_info["special_message"] = false;
    }
    $template = $twig->load('biblio.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page_root.html.twig');
}
echo $template->display($template_info);
