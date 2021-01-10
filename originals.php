<?php
require_once __DIR__.'/globals.php';
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Свыше 3000 стихов 429 авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';
$template_info["title"] ='Оригиналы стихов';
$records = array();
if ($_GET['action'] == 'showall') {
    $template_info["header"] ='Все оригиналы';
    $template_info["showall"] = true;
    $template_info["search"] = false;
    $records = getListfromOriginals();
#    print_r($records);
    $final = makeFinalArray ($records);
#    print_r($final);
    $template_info["final"] = $final;
    $template_info["title"] ='Все оригиналы стихов';
    $template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Все оригиналы стихов';
    $template = $twig->load('originals_list.html.twig');
}
elseif ( ($_GET['action'] == 'show') && ($_GET['record_id'] > 0) ){
    $record_id = $_GET['record_id'];
    list($originals_id,$author_id,$proper_name, $dates,$epoch,$cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru,
    $poem_name_zh, $poem_name_ru,$poem_code,$biblio_id,$poem_text,$genres,$size, $zh_trad, $zh_simple) = getOriginalsByPoemID($record_id);
    $zname = '';
    if ($zh_trad) {
        $zname = '&nbsp;<span class="name zh">'.$zh_trad.'</span>';
    }
    else if ($zh_simple) {
        $zname = '&nbsp;<span class="name zh">'.$zh_simple.'</span>';
    }
    $header = '<a href="./authors.php?action=show&record_id='.$author_id.'"><span class="author name">'
    .$proper_name.'</span> <span class="author dates">'.$dates.'</span></a>'.$zname.'<span class="epoch">'.$epoch.'</span>';
    $template_info["header"] = $header;
    $template_info["author_id"] = $author_id;
    $template_info["poem_name"] = '<span class="poem_name zh">'.$poem_name_zh.'</span> <span class="poem_name ru">'.$poem_name_ru.'</span>';
    $template_info["poem_text"] = $poem_text;
    if ($cycle_ru) {
        $cycle = '<span class="cycle zh">'.$cycle_zh.'</span> <span class="cycle ru">'.$cycle_ru.'</span>';
    }
    else { $cycle = false; }
    if ($subcycle_ru) {
        $subcycle = '<span class="subcycle zh">'.$subcycle_zh.'</span> <span class="subcycle ru">'.$subcycle_ru.'</span>';
    }
    else { $subcycle = false; }
    $entries = getOtherTranslationsByPoemCode($poem_code);
    if (!empty($entries)) {
        $otherTranslations = array();
        foreach ($entries as $entry) {
            list($id, $tr1, $tr2, $tr_full_name, $name_zh, $name_ru) = $entry;
            $trans = '<span class="translators">'.$tr_full_name.'</span>';
            $name = makePoemName($name_zh, $name_ru);
            array_push($otherTranslations, array('translator' => $trans, 'tr_id' =>$tr1, 'poem_name' => $name, 'id' => $id));
        }       
        $otherTranslations = array('header' => 'Переводы', 'translations' => $otherTranslations);
        $template_info["otherTranslation"] = $otherTranslations;
    }
    else {
        $otherTranslations = array('header' => 'Пожалуйста, помогите найти перевод!', 'translations' => []);
        $template_info["otherTranslation"] = $otherTranslations;
    }

    $template_info["cycle"] = $cycle;
    $template_info["subcycle"] = $subcycle;
    $template_info["biblio"] = false;
    if ($biblio_id) {
        list($biblio_ref_name) = getBiblioByID($biblio_id);
        $biblio = array($biblio_ref_name, $biblio_id);
        $template_info["biblio"] = $biblio;
    }
    $template_info["translator"] = false;
    $template_info["original"] = false;
    $template_info["topics"] = false;
    $template_info["title"] = $template_info["title"].' '.$proper_name.' '.$dates;
    if ($cycle) {
        if ($poem_name_zh && $cycle_zh) {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_zh.' '.$poem_name_ru.'" из цикла "'.$cycle_zh.' '.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'.';
        }
        elseif ($poem_name_zh && !$cycle_zh){
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_zh.' '.$poem_name_ru.'" из цикла "'.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'.';
        }
        elseif (!$poem_name_zh && $cycle_zh){
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_ru.'" из цикла "'.$cycle_zh.' '.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'.';
        }
        else {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_ru.'" из цикла "'.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'.';
        }
    }
    else {
        if ($poem_name_zh) {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_zh.' '.$poem_name_ru.'". Автор: '.$proper_name.' '.$dates.'.';
        }
        else {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_ru.'". Автор: '.$proper_name.' '.$dates.'.';
        }
    }
    if (stripos($cycle_ru, 'Из') !== false) {
        $fromcycle = true;
    }
    else {
        $fromcycle = false;
    }
    $template_info["fromcycle"] = $fromcycle;
    $template = $twig->load('poem.html.twig');
}
elseif ($_GET['action'] == 'search') {
    $template_info["header"] ='Поиск по оригиналам стихов';
    if (array_key_exists('posted', $_GET)) {
        $template_info["showall"] = true;
        $template_info["search"] = false;
        $pattern = $_POST['pattern'];
        $poems_ids = array(); 
        $poems_ids = searchOriginals($pattern);
        $rs = array();
        foreach($poems_ids as $id) {
            $r = getRecordfromOriginals($id);
            array_push($rs,$r);
        }
        $records = array();
        foreach ($rs as $record) {
            array_push($records,$record[0]);
        }
        $final = makeFinalArray ($records);
        $template_info["final"] = $final;
        if (count($records) < 1) {
            $template_info["header"] ='Ничего не найдено';
        }
    }
    else {
        $template_info["search"] = true;
        $template_info["showall"] = false;
    }
    $template_info["title"] ='Поиск по оригиналам стихов';
    $template = $twig->load('originals_list.html.twig');
}

else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page_root.html.twig');
}
echo $template->display($template_info);

function makeFinalArray ($records) {
    $new_arr = array();
    $arrAuthors = array();
    $final = array();
    for ($i=0; $i < count($records) ; $i++) {
        $author_id = $records[$i][1];
        $proper_name = $records[$i][2];
        $dates = $records[$i][3];
        $epoch = $records[$i][12];
        $zname = '';
        if ($records[$i][13]) {
            $zname = '&nbsp;<span class="name zh">'.$records[$i][13].'</span>';
        }
        else if ($records[$i][14]) {
            $zname = '&nbsp;<span class="name zh">'.$records[$i][14].'</span>';
        }
        $author = '<span class="author name">'.$proper_name.'</span> <span class="author dates">'.$dates.'</span>#'.$author_id.'#'.$epoch.'#'.$zname;
#        $cycle = '<span class="cycle zh">'.$records[$i][4].'</span> <span class="cycle ru">'.$records[$i][5].'</span>';
        array_push($arrAuthors, $author);
        if (array_key_exists($author, $new_arr)) {
            array_push($new_arr[$author],  $records[$i]); 
        }
        else {
            $new_arr[$author] = array($records[$i]);
        }    
    }
    $arrAuthors = array_unique($arrAuthors);
    foreach ($arrAuthors as  $author) {
        $cycles = array();
        $poems = array();
        for ($i=0; $i < count($new_arr[$author]) ; $i++) {
            $poem = $new_arr[$author][$i];
            $cycle = '<span class="cycle zh">'.$poem[4].'</span> <span class="cycle ru">'.$poem[5].'</span>';
            if ($cycle == '<span class="cycle zh"></span> <span class="cycle ru"></span>') {
                $cycle = 'default'.$i;
            }
            $subcycle = '<span class="subcycle zh">'.$poem[6].'</span> <span class="subcycle ru">'.$poem[7].'</span>';
            if ($subcycle == '<span class="subcycle zh"></span> <span class="subcycle ru"></span>') {
                $subcycle = 'default'.$i;
            }
            if (!array_key_exists($cycle, $poems)) {
                $poems[$cycle] = array();
            }
            if (!array_key_exists($subcycle, $poems[$cycle])) {
                $poems[$cycle][$subcycle] = array();
            }
            array_push($poems[$cycle][$subcycle], $poem);
        }
        array_push($final, array('author' => $author, 'poems' => $poems));
    }
    return $final;
}
