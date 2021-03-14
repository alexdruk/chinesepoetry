<?php
require_once __DIR__.'/globals.php';
$template_info["title"] ='Стихи';
$template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Свыше 3000 стихов 429 авторов от VI в. до н.э. вплоть до наших дней в лучших переводах.';

$records = array();
if ($_GET['action'] == 'byAuthor') {
    $template_info["header"] ='По авторам';
    $records = getAllfromAuthors();
//    list($author_id, $full_name, $proper_name, $dates,  $epoch, $present, $zh_trad, $zh_simple) = $records;
    $template_info["search"] = false;
#    $template_info["byTranslator"] = false;
#    $template_info["byTopic"] = false;
    $template_info["byAuthor"] = true;
    $template_info["records"] = $records;
//    print_r($records);
    $template_info["showall"] = true;
    $template_info["show_alphabet"] = true;
    $template_info["title"] ='Стихи по авторам';
    $template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Все стихи по авторам.';
    $template = $twig->load('authors_showall.html.twig');
}
elseif ($_GET['action'] == 'byTranslator') {
    $template_info["header"] ='По переводчикам';
    $records = getAllfromPresentTranslators();
#    list($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present) = $records;
    $template_info["search"] = false;
#    $template_info["byAuthor"] = false;
    $template_info["showall"] = true;
    $template_info["byTranslator"] = true;
    $template_info["byTopic"] = false;
    $template_info["records"] = $records;
    $template_info["title"] ='Стихи по переводчикам';
    $template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Все стихи по переводчикам.';
    $template = $twig->load('translators_showall.html.twig');
}
elseif ($_GET['action'] == 'byEpoch') {
    $template_info["header"] ='По эпохам';
    $template_info["search"] = false;
    $template_info["byTranslator"] = false;
    $template_info["byTopic"] = false;
    $template_info["byAuthor"] = true;
    if (array_key_exists('posted', $_GET)) {
        $epoch = $_GET['epoch']; 
        $records = getAllfromAuthorsByEpoch($epoch);
        $template_info["records"] = $records;
        $template_info["title"] ='Все стихи эпохи: "'.$epoch.'"';
        $template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Все стихи эпохи: "'.$epoch.'"';
        $template = $twig->load('poems_showall.html.twig');
    }
    else {
        $template = $twig->load('authors_byEpoch.html.twig');
    }
}
elseif ($_GET['action'] == 'byTopic') {
    $template_info["search"] = false;
    $template_info["byTranslator"] = false;
    $template_info["byAuthor"] = false;
    $template_info["byTopic"] = true;
    if (array_key_exists('posted', $_GET)) {
        $topic_id = $_GET['topic_id']; 
        $records = getAllfromAuthorsByTopic($topic_id);
        $new_records = array();
//        print_r($records);
        foreach ($records as $record) {
            array_push($record,  $topic_id);
            array_push($new_records, $record);
        }
//        print_r($new_records);
        list($topics_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic_id);
        $template_info["header"] = 'Тема: '.$topic_name;
        $template_info["records"] = $new_records;
        $template_info["title"] ='Все стихи по теме: "'.$topic_name.'"';
        $template_info["page_description"] = 'Антология современной и старинной китайской поэзии. Все стихи по теме: "'.$topic_name.'"';
        $template = $twig->load('poems_showall.html.twig');
    }
    else {
        $template_info["header"] ='По темам';
        $template_info["byTopic"] = true;
        $template = $twig->load('authors_byTopic.html.twig');
    }
}
elseif  ( ($_GET['action'] == 'bySource') && (array_key_exists('biblio_id', $_GET))  ){
    $biblio_id = $_GET['biblio_id'];
    $records = getWithoutPoem_textFromPoemsByBiblioID($biblio_id);
    $recordsForAuthor = array();
    foreach ($records as $record) {
//        if ($record[1] == $author_id) {
            array_push($recordsForAuthor, $record);
//        }
    }
#print_r($recordsForAuthor);
    $final = makeFinalArray ($recordsForAuthor);
#    print_r($final);
    $template_info["header"] = 'Из источника';
    $template_info["byAuthor"] = true;
    $template_info["byTranslator"] = false;
    $template_info["final"] = $final;
    $template_info["title"] ='Все стихи из источника';
    $template = $twig->load('at_list.html.twig');
}
elseif ($_GET['action'] == 'showrandom') {
    $poem_id = getRandomPoemID();
    $loc = "Location: poems.php?action=show&poem_id=".$poem_id."&r=".mt_rand(0, 9999999);
    header($loc);
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Pragma: no-cache"); // HTTP/1.0
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    }
elseif ($_GET['action'] == 'search') {
    $template_info["header"] ='Полнотекстный поиск по переводам стихов';
    if (array_key_exists('posted', $_GET)) {
        $template_info["search"] = false;
        $template_info["byTranslator"] = false;
        $template_info["byTopic"] = false;
        $template_info["byAuthor"] = true;
        $pattern = $_POST['pattern']; 
        $records = searchPoems($pattern);
        if (count($records) < 1) {
            $template_info["header"] ='Ничего не найдено';
        }
        $final = array();
        $final = makeFinalArray ($records);
#print_r($final);
        $template_info["final"] = $final;
        $template_info["title"] ='Полнотекстный поиск по переводам стихов';
        $template = $twig->load('at_list.html.twig');
    }
    else {
        $template_info["search"] = true;
        $template_info["byTranslator"] = false;
        $template_info["byTopic"] = false;
        $template_info["byAuthor"] = false;
        $template = $twig->load('poems_showall.html.twig');
    }
}
elseif  ( ($_GET['action'] == 'show') && (array_key_exists('author_id', $_GET)) && 
    (!array_key_exists('topic_id', $_GET)) && (!array_key_exists('poem_id', $_GET)) ){
    $author_id = $_GET['author_id'];
    $records = getWithoutPoem_textFromPoemsByAuthorID($author_id);
    $final = makeFinaTranslatorslArray ($records);
//    print_r($final);
    list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
    $template_info["header"] = $author_html;
    $template_info["byAuthor"] = false;
    $template_info["byTranslator"] = true;
    $template_info["final"] = $final;
#now try to get record from originals
    $orig_records = getOriginalsByAuthorID($author_id);
    $template_info["has_originals"] = false;
    if (count($orig_records) > 0 ) {
        $template_info["has_originals"] = true;
        $orig_final = makeFinalArray($orig_records);
        $template_info["orig_final"] = $orig_final;
#print_r($orig_final);
    }
    list($junk, , $proper_name,  $dates,  $epoch, ) = getByIDFromAuthors($author_id);
    $template_info["title"] ='Все стихи '.$proper_name.' '.$dates;
    $template = $twig->load('at_list.html.twig');
}

elseif  ( ($_GET['action'] == 'show') && (array_key_exists('translator_id', $_GET)) && (!array_key_exists('poem_id', $_GET)) ){
    $translator_id = $_GET['translator_id'];
    $records = getWithoutPoem_textFromPoemsByTranslatorID($translator_id);
    list($junk, $tr_full_name, , , , , , , , , , ) = getByIDFromTranslators($translator_id);
    $translator = '<a href="./translators.php?action=show&record_id='.$translator_id.'">'.$tr_full_name.'</a>';
    $translator = '<span class="translators name">'.$translator.'</span>';
    $final = array();
    $final = makeFinalArray ($records);
    $template_info["header"] = $translator;
    $template_info["byAuthor"] = true;
    $template_info["byTranslator"] = false;
    $template_info["final"] = $final;
    $template_info["title"] = $tr_full_name.' - все переводы';
    $template = $twig->load('at_list.html.twig');

}
elseif  ( ($_GET['action'] == 'show') && (array_key_exists('topic_id', $_GET)) && 
    (array_key_exists('author_id', $_GET)) && (!array_key_exists('poem_id', $_GET)) ){
    $topic_id = $_GET['topic_id'];
    $author_id = $_GET['author_id'];
    $records = getWithoutPoem_textFromPoemsByTopicID($topic_id);
    $recordsForAuthor = array();
    foreach ($records as $record) {
        if ($record[1] == $author_id) {
            array_push($recordsForAuthor, $record);
        }
    }
    $final = makeFinalArraybyTopic ($recordsForAuthor); //to not make links for cycles  and subcycles
 
    list($topics_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic_id);
    $template_info["header"] = 'Тема: '.$topic_name;
    $template_info["byAuthor"] = true;
    $template_info["byTranslator"] = false;
    $template_info["final"] = $final;
    $template_info["title"] ='Все стихи по теме: "'.$topic_name.'"';
    $template = $twig->load('at_list.html.twig');
}

elseif ( ($_GET['action'] == 'show')  && ($_GET['poem_id'] > 0) ){
    #MAIN CODE TO SHOW POEMS
    $poem_id = $_GET['poem_id'];
    $records = getPoemsByPoemID($poem_id);

    list($poems_id,$author_id,$translator1_id,$translator2_id,
    $topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
    $poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poem_hash,$site, $siteURL) = $records[0];
    list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
    $author = $author_html;
    $translator = makeTranslator($translator1_id, $translator2_id);
    $poem_name = makePoemName($poem_name_zh, $poem_name_ru);
    $template_info["original"] = false;
    $template_info["otherTranslation"] = false;
    if ($poem_code) {
        list($originals_id, $nam_zh, $nam_ru) = getOriginalByPoemCode($poem_code);;
        $originals = array();
        if ($originals_id) {
            $nam = makePoemName($nam_zh, $nam_ru);
            $orig = '<a href ="./originals.php?action=show&record_id='.$originals_id.'">'.$nam.'</a>';
            array_push($originals,$orig);
            $template_info["original"] = $originals;
        }
        $entries = getOtherTranslationsByPoemCode($poem_code);
        if (!empty($entries)) {
            $otherTranslations = array();
            foreach ($entries as $entry) {
               if ($entry[0] == $poems_id) { continue;}; #skip if the same id as current poem
                list($id, $tr1, $tr2, $tr_full_name, $name_zh, $name_ru) = $entry;
                $trans = '<span class="translators">'.$tr_full_name.'</span>';
                $name = makePoemName($name_zh, $name_ru);
                array_push($otherTranslations, array('translator' => $trans, 'tr_id' =>$tr1, 'poem_name' => $name, 'id' => $id));
            }
            if (!empty($otherTranslations)) {
                $otherTranslations = array('header' => 'Другие переводы', 'translations' => $otherTranslations);
                $template_info["otherTranslation"] = $otherTranslations;
            }
       }
    }
    $topics = maketopics($topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id);
    $template_info["topics"] = $topics;
    if (stripos($cycle_ru, 'Из') !== false) {
        $fromcycle = true;
    }
    else {
        $fromcycle = false;
    }
    if ((stripos($cycle_ru, '("') !== false) && ($cycle_zh)) {
        $melody = true;
        $fromcycle = true;
    }
    else {
        $melody = false;
    }
    $template_info["melody"] = $melody;
    $cycle = makeCycle($cycle_ru, $cycle_zh,$translator1_id);
    $subcycle = makeSubCycle($subcycle_ru, $subcycle_zh,$translator1_id);
    $template_info["header"] = $author;
    $template_info["author_id"] = $author_id;
    $template_info["translator"] = $translator;
    $template_info["poem_name"] = $poem_name;
    $template_info["poem_text"] = $poem_text;
    $template_info["cycle"] = $cycle;
    $template_info["fromcycle"] = $fromcycle;
    $template_info["subcycle"] = $subcycle;
    $template_info["biblio"] = false;
    if ($biblio_id) {
        list($biblio_ref_name) = getBiblioByID($biblio_id);
        $biblio = array($biblio_ref_name, $biblio_id);
        $template_info["biblio"] = $biblio;
    }
    $template_info["title"] = $template_info["title"].' | '.$poem_name_ru;
    $transl = strip_tags($translator);
    if ($cycle) {
        if ($poem_name_zh && $cycle_zh) {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_zh.' '.$poem_name_ru.'" из цикла "'.$cycle_zh.' '.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'. Перевод: '.$transl;
        }
        elseif ($poem_name_zh && !$cycle_zh){
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_zh.' '.$poem_name_ru.'" из цикла "'.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'. Перевод: '.$transl;
        }
        elseif (!$poem_name_zh && $cycle_zh){
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_ru.'" из цикла "'.$cycle_zh.' '.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'. Перевод: '.$transl;
        }
        else {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_ru.'" из цикла "'.$cycle_ru.'". Автор: '.$proper_name.' '.$dates.'. Перевод: '.$transl;
        }
    }
    else {
        if ($poem_name_zh) {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_zh.' '.$poem_name_ru.'". Автор: '.$proper_name.' '.$dates.'. Перевод: '.$transl;
        }
        else {
            $template_info["page_description"] = 'Стихотворение "'.$poem_name_ru.'". Автор: '.$proper_name.' '.$dates.'. Перевод: '.$transl;
        }
    }
    $template_info["genres"] = false;
    $template_info["size"] = false;
    $template_info["site"] = $site;
    $template_info["siteURL"] = $siteURL;
    $ip = getIP();
    $countIPs = countIP($ip );
    $countIP = false;
    if ($countIPs < 30) {
        $countIP = true;
    }
    $template_info["countIP"] = $countIP;
    $template_info["poems_id"] = $poems_id;
    $template = $twig->load('poem.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page_root.html.twig');
}
echo $template->display($template_info);
