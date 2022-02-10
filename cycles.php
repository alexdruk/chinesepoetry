<?php
require_once __DIR__.'/globals.php';
$records = array();
if (isset($_GET['cycle']) && isset($_GET['translator'])) {
    $cycle = $_GET['cycle'];
    $translator_id = $_GET['translator'];

    $records = getPoemsByCycleTranslator($cycle, $translator_id);

    $records = sortRecordsWithoutSubcycle($records);
    $author_id = $records[0][1];
    // print_r($records);
    list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
    list($poems_id, $author_id, $proper_name, $dates, $epoch, $translator1_id, $translator2_id,
    $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_name_zh, $poem_name_ru, $poem_text) = $records[0];
    $translator = makeTranslator($translator1_id, $translator2_id);
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
    if ($melody) {
        $prev_auth = $author_id;
        for ($i=0; $i < count($records); $i++) {
            $current_auth = $records[$i][1];
            if ($current_auth !== $prev_auth) {
                array_push($records[$i], "NEW");
                $prev_auth = $current_auth;
            }    
        }
    }
    // print_r($records);

    $cycle = makeCycle($cycle_ru, $cycle_zh,$translator_id );
    $subcycle = makeSubCycle($subcycle_ru, $subcycle_zh,$translator_id);
    $template_info["records"] = $records;
    $template_info["originals"] = false;
    $template_info["header"] = $author_html;
    $template_info["translator"] = $translator;
    $template_info["translator_id"] = $translator1_id;
    $template_info["melody"] = $melody;
    $template_info["cycle"] = $cycle;
    $template_info["fromcycle"] = $fromcycle;
    $template_info["subcycle"] = $subcycle;
    $template_info["title"] = $cycle_ru;
    $template_info["page_description"] = $cycle_ru;
    }
elseif ((isset($_GET['subcycle'])) && isset($_GET['translator'])) {
    $subcycle = $_GET['subcycle'];
    $translator_id = $_GET['translator'];
    $records = getPoemsBySubCycleTranslator($subcycle, $translator_id);
    $records = sortRecordsWithoutSubcycle($records);
    $author_id = $records[0][1];
    list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
    list($poems_id, $author_id, $proper_name, $dates, $epoch, $translator1_id, $translator2_id,
    $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_name_zh, $poem_name_ru, $poem_text) = $records[0];
    $translator = makeTranslator($translator1_id, $translator2_id);
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
    $cycle = makeCycle($cycle_ru, $cycle_zh,$translator_id );
    $subcycle = makeSubCycle($subcycle_ru, $subcycle_zh,$translator_id);
    $template_info["records"] = $records;
    $template_info["originals"] = false;
    $template_info["header"] = $author_html;
    $template_info["translator"] = $translator;
    $template_info["translator_id"] = $translator1_id;
    $template_info["melody"] = $melody;
    $template_info["cycle"] = $cycle;
    $template_info["fromcycle"] = $fromcycle;
    $template_info["subcycle"] = $subcycle;
    $template_info["title"] = $subcycle_ru;
    $template_info["page_description"] = $subcycle_ru;

}
elseif (isset($_GET['cycle_zh'])) {
    $cycle_zh = $_GET['cycle_zh'];
    $records = getOriginalsByCycleZH($cycle_zh );
    // print_r($records);
    $records = sortRecordsWithoutSubcycleOrig($records);
    list($originals_id,$author_id, $proper_name, $dates, $epoch,
    $cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text) = $records[0];
//    print_r($records[0]);
    list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
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
    if ($cycle_ru && $cycle_zh) {
        $cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($cycle_zh).'">'.$cycle_zh.'</a></span> 
		<span class="cycle ru">'.$cycle_ru.'</span>';
    }
    elseif ($cycle_ru && !$cycle_zh) {
        $cycle = '<span class="cycle ru">'.$cycle_ru.'</span>';
    }
    else {
        $cycle = false;
    }
    if ($subcycle_ru && $subcycle_zh) {
        $subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($subcycle_zh).'">'.$subcycle_zh.'</a></span> <span class="subcycle ru">'.$subcycle_ru.'</span>';
    }
    elseif($subcycle_ru && !$subcycle_zh){
        $subcycle = '<span class="subcycle ru">'.$subcycle_ru.'</span>';
    }
    else {
        $subcycle = false;
    }
    if ($melody) {
        $prev_auth = $author_id;
        for ($i=0; $i < count($records); $i++) {
            $current_auth = $records[$i][1];
            if ($current_auth !== $prev_auth) {
                array_push($records[$i], "NEW");
                $prev_auth = $current_auth;
            }    
        }
    }    
    // print_r($records);

    $template_info["records"] = $records;
    $template_info["originals"] = true;
    $template_info["header"] = $author_html;
    $template_info["translator"] = false;
    $template_info["translator_id"] = '';
    $template_info["melody"] = $melody;
    $template_info["cycle"] = $cycle;
    $template_info["fromcycle"] = $fromcycle;
    $template_info["subcycle"] = $subcycle;
    $template_info["title"] = $cycle_ru;
    $template_info["page_description"] = $cycle_ru;
}
elseif (isset($_GET['subcycle_zh'])) {
    $subcycle_zh = $_GET['subcycle_zh'];
    $records = getOriginalsBySubCycleZH($subcycle_zh );
    $records = sortRecordsWithoutSubcycleOrig($records);
    list($originals_id,$author_id, $proper_name, $dates, $epoch,
    $cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text) = $records[0];
    list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
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
    if ($cycle_ru && $cycle_zh) {
        $cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($cycle_zh).'">'.$cycle_zh.'</a></span> 
		<span class="cycle ru">'.$cycle_ru.'</span>';
    }
    elseif ($cycle_ru && !$cycle_zh) {
        $cycle = '<span class="cycle ru">'.$cycle_ru.'</span>';
    }
    if ($subcycle_ru && $subcycle_zh) {
        $subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($subcycle_zh).'">'.$subcycle_zh.'</a></span> <span class="subcycle ru">'.$subcycle_ru.'</span>';
    }
    elseif($subcycle_ru && !$subcycle_zh){
        $subcycle = '<span class="subcycle ru">'.$subcycle_ru.'</span>';
    }

    $template_info["records"] = $records;
    $template_info["originals"] = true;
    $template_info["header"] = $author_html;
    $template_info["translator"] = false;
    $template_info["translator_id"] = '';
    $template_info["melody"] = $melody;
    $template_info["cycle"] = $cycle;
    $template_info["fromcycle"] = $fromcycle;
    $template_info["subcycle"] = $subcycle;
    $template_info["title"] = $subcycle_ru;
    $template_info["page_description"] = $subcycle_ru;
}
$template = $twig->load('cycle.html.twig');
echo $template->display($template_info);

function sortRecordsWithoutSubcycle ($records) {
    $prev_subcycle_ru = null;
    $prev_subcycle_zh = null;
    for ($i=0; $i < count($records) ; $i++) { 
        if (($prev_subcycle_zh === $records[$i][9]) && ($prev_subcycle_ru === $records[$i][10])){
            $records[$i][9] = '';
            $records[$i][10] = '';
        } else {
            $prev_subcycle_zh = $records[$i][9];
            $prev_subcycle_ru = $records[$i][10];
        }
    }
    return $records;
}
function sortRecordsWithoutSubcycleOrig ($records) {
    $prev_subcycle_ru = null;
    $prev_subcycle_zh = null;
    for ($i=0; $i < count($records) ; $i++) { 
        if (($prev_subcycle_zh === $records[$i][7]) && ($prev_subcycle_ru === $records[$i][8])){
            $records[$i][7] = '';
            $records[$i][8] = '';
        } else {
            $prev_subcycle_zh = $records[$i][7];
            $prev_subcycle_ru = $records[$i][8];
        }
    }
    return $records;
}