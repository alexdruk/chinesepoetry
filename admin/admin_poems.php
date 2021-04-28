<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Стихи';
if (array_key_exists('action', $_GET)) { 
    $records = array();
    if ($_GET['action'] == 'insert') {
        $template_info["header"] ='Добавить стих';
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('poem_insert_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
#			$_POST = array_map("addslashes",$_POST); 			
			$error = false;
			$success = false;
            $author_id = $_POST['author_id'];
            $translator1_id = $_POST['translator1_id'];
            $translator2_id = $_POST['translator2_id']; 
            $topic1_id = $_POST['topic1_id']; 
            $topic2_id = $_POST['topic2_id']; 
            $topic3_id = $_POST['topic3_id']; 
            $topic4_id = $_POST['topic4_id']; 
            $topic5_id = $_POST['topic5_id']; 
            $cycle_zh = $_POST['cycle_zh']; 
            $cycle_ru = $_POST['cycle_ru']; 
            $corder = $_POST['corder']; 
            $subcycle_zh = $_POST['subcycle_zh']; 
            $subcycle_ru = $_POST['subcycle_ru']; 
            $scorder = $_POST['scorder']; 
            $poem_name_zh = $_POST['poem_name_zh']; 
            $poem_name_ru = $_POST['poem_name_ru']; 
            $poem_code = $_POST['poem_code']; 
            $biblio_id = $_POST['biblio_id']; 
            $poem_text = $_POST['poem_text'];
            $totallines  = $_POST['totallines'];
            $fulllines  = $_POST['fulllines'];
            $genres  = $_POST['genres'];
            $site = $_POST['site'];;
            $siteURL = $_POST['siteURL'];;
            if (array_key_exists('translator2_id', $_POST)) {
                $translator2_id = (!empty($_POST['translator2_id'])) ? $_POST['translator2_id'] : NULL;
            }
            if (array_key_exists('topic2_id', $_POST)) {
                $topic2_id = (!empty($_POST['topic2_id'])) ? $_POST['topic2_id'] : NULL;
            }
            if (array_key_exists('topic3_id', $_POST)) {
                $topic3_id = (!empty($_POST['topic3_id'])) ? $_POST['topic3_id'] : NULL;
            }
            if (array_key_exists('topic4_id', $_POST)) {
                $topic4_id = (!empty($_POST['topic4_id'])) ? $_POST['topic4_id'] : NULL;
            }
            if (array_key_exists('topic5_id', $_POST)) {
                $topic5_id = (!empty($_POST['topic5_id'])) ? $_POST['topic5_id'] : NULL;
            }
            if (array_key_exists('cycle_zh', $_POST)) {
                $cycle_zh = (!empty($_POST['cycle_zh'])) ? $_POST['cycle_zh'] : NULL;
            }
            if (array_key_exists('cycle_ru', $_POST)) {
                $cycle_ru = (!empty($_POST['cycle_ru'])) ? $_POST['cycle_ru'] : NULL;
            }
            if (array_key_exists('corder', $_POST)) {
                $corder = (!empty($_POST['corder'])) ? $_POST['corder'] : NULL;
            }
            if (array_key_exists('subcycle_zh', $_POST)) {
                $subcycle_zh = (!empty($_POST['subcycle_zh'])) ? $_POST['subcycle_zh'] : NULL;
            }
            if (array_key_exists('subcycle_ru', $_POST)) {
                $subcycle_ru = (!empty($_POST['subcycle_ru'])) ? $_POST['subcycle_ru'] : NULL;
            }
            if (array_key_exists('scorder', $_POST)) {
                $scorder = (!empty($_POST['scorder'])) ? $_POST['scorder'] : NULL;
            }
            if (array_key_exists('poem_name_zh', $_POST)) {
                $poem_name_zh = (!empty($_POST['poem_name_zh'])) ? $_POST['poem_name_zh'] : NULL;
            }
            if (array_key_exists('poem_code', $_POST)) {
                $poem_code = (!empty($_POST['poem_code'])) ? $_POST['poem_code'] : NULL;
            }
            if (array_key_exists('biblio_id', $_POST)) {
                $biblio_id = (!empty($_POST['biblio_id'])) ? $_POST['biblio_id'] : NULL;
            }
            if (array_key_exists('totallines', $_POST)) {
                $totallines = (!empty($_POST['totallines'])) ? $_POST['totallines'] : NULL;
            }
            if (array_key_exists('fulllines', $_POST)) {
                $fulllines = (!empty($_POST['fulllines'])) ? $_POST['fulllines'] : NULL;
            }
            if (array_key_exists('genres', $_POST)) {
                $genres = (!empty($_POST['genres'])) ? $_POST['genres'] : NULL;
            }
            if (array_key_exists('site', $_POST)) {
                $site = (!empty($_POST['site'])) ? $_POST['site'] : NULL;
            }
            if (array_key_exists('siteURL', $_POST)) {
                $siteURL = (!empty($_POST['siteURL'])) ? $_POST['siteURL'] : NULL;
            }
            $poem_hash = md5($poem_text);
			$r_id = poems_insert_record($author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poem_hash,$totallines,$fulllines,$genres,$site,$siteURL);
			if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
			$template = $twig->load('poem_insert_form.html.twig');
		}
    }
   if ($_GET['action'] == 'modify') {
    $template_info["header"] ='Редактировать стих';
    if (preg_match("/^\d+$/", $_GET['record_id'])) {			
        // if POST present 
        if ((array_key_exists('posted', $_GET))  && (!empty($_POST))){
            $poems_id = $_GET['record_id'];
            $author_id = $_POST['author_id'];
            $translator1_id = $_POST['translator1_id'];
            $topic1_id = $_POST['topic1_id'];
            $poem_name_ru = $_POST['poem_name_ru'];
            $poem_text = $_POST['poem_text'];
            if (array_key_exists('translator2_id', $_POST)) {
                $translator2_id = (!empty($_POST['translator2_id'])) ? $_POST['translator2_id'] : NULL;
            }
            if (array_key_exists('topic2_id', $_POST)) {
                $topic2_id = (!empty($_POST['topic2_id'])) ? $_POST['topic2_id'] : NULL;
            }
            if (array_key_exists('topic3_id', $_POST)) {
                $topic3_id = (!empty($_POST['topic3_id'])) ? $_POST['topic3_id'] : NULL;
            }
            if (array_key_exists('topic4_id', $_POST)) {
                $topic4_id = (!empty($_POST['topic4_id'])) ? $_POST['topic4_id'] : NULL;
            }
            if (array_key_exists('topic5_id', $_POST)) {
                $topic5_id = (!empty($_POST['topic5_id'])) ? $_POST['topic5_id'] : NULL;
            }
            if (array_key_exists('cycle_zh', $_POST)) {
                $cycle_zh = (!empty($_POST['cycle_zh'])) ? $_POST['cycle_zh'] : NULL;
            }
            if (array_key_exists('cycle_ru', $_POST)) {
                $cycle_ru = (!empty($_POST['cycle_ru'])) ? $_POST['cycle_ru'] : NULL;
            }
            if (array_key_exists('corder', $_POST)) {
                $corder = (!empty($_POST['corder'])) ? $_POST['corder'] : NULL;
            }
            if (array_key_exists('subcycle_zh', $_POST)) {
                $subcycle_zh = (!empty($_POST['subcycle_zh'])) ? $_POST['subcycle_zh'] : NULL;
            }
            if (array_key_exists('subcycle_ru', $_POST)) {
                $subcycle_ru = (!empty($_POST['subcycle_ru'])) ? $_POST['subcycle_ru'] : NULL;
            }
            if (array_key_exists('scorder', $_POST)) {
                $scorder = (!empty($_POST['scorder'])) ? $_POST['scorder'] : NULL;
            }
            if (array_key_exists('poem_name_zh', $_POST)) {
                $poem_name_zh = (!empty($_POST['poem_name_zh'])) ? $_POST['poem_name_zh'] : NULL;
            }
            if (array_key_exists('poem_code', $_POST)) {
                $poem_code = (!empty($_POST['poem_code'])) ? $_POST['poem_code'] : NULL;
            }
            if (array_key_exists('biblio_id', $_POST)) {
                $biblio_id = (!empty($_POST['biblio_id'])) ? $_POST['biblio_id'] : NULL;
            }
            if (array_key_exists('totallines', $_POST)) {
                $totallines = (!empty($_POST['totallines'])) ? $_POST['totallines'] : NULL;
            }
            if (array_key_exists('fulllines', $_POST)) {
                $fulllines = (!empty($_POST['fulllines'])) ? $_POST['fulllines'] : NULL;
            }
            if (array_key_exists('genres', $_POST)) {
                $genres = (!empty($_POST['genres'])) ? $_POST['genres'] : NULL;
            }
            if (array_key_exists('site', $_POST)) {
                $site = (!empty($_POST['site'])) ? $_POST['site'] : NULL;
            }
            if (array_key_exists('siteURL', $_POST)) {
                $siteURL = (!empty($_POST['siteURL'])) ? $_POST['siteURL'] : NULL;
            }
            $template_info["record_id"] = $poems_id;
            $template_info["author_id"] = $author_id;
            $template_info["translator1_id"] = $translator1_id;
            $template_info["translator2_id"] = $translator2_id;
            $template_info["topic1_id"] = $topic1_id;
            $template_info["topic2_id"] = $topic2_id;
            $template_info["topic3_id"] = $topic3_id;
            $template_info["topic4_id"] = $topic4_id;
            $template_info["topic5_id"] = $topic5_id;
            $template_info["cycle_zh"] = $cycle_zh;
            $template_info["cycle_ru"] = $cycle_ru;
            $template_info["corder"] = $corder;
            $template_info["subcycle_zh"] = $subcycle_zh;
            $template_info["subcycle_ru"] = $subcycle_ru;
            $template_info["scorder"] = $scorder;
            $template_info["poem_name_zh"] = $poem_name_zh;
            $template_info["poem_name_ru"] = $poem_name_ru;
            $template_info["poem_code"] = $poem_code;
            $template_info["biblio_id"] = $biblio_id;
            $template_info["poem_text"] = $poem_text;
            $template_info["totallines"] = $totallines;
            $template_info["fulllines"] = $fulllines;
            $template_info["genres"] = $genres;
            $template_info["site"] = $site;
            $template_info["siteURL"] = $siteURL;
          $r_id = updatePoemsByID($poems_id,$author_id,$translator1_id,$translator2_id,
          $topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
          $poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$totallines,$fulllines,$genres,$site,$siteURL);
            if ($r_id > 0) {
                $success = 'Success! A record was updated.';
                $error = false;
                $template_info["error"] = $error;
                $template_info["success"] = $success;
            }
            else {
                $error = 'An error occur! See DB log files. Or just nothing changed - check table.';
                $template_info["error"] = $error;
                $template_info["success"] = false;
            }
        }
        // if NO  POST (just to show prefilled form) 
        else {
            $record = array();
            $record = getByIDFromPoems($_GET['record_id']);
            list($author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$totallines,$fulllines,$genres,$site, $siteURL) = $record;
            $template_info["record_id"] = $_GET['record_id'];
            $template_info["author_id"] = $author_id;
            $template_info["translator1_id"] = $translator1_id;
            $template_info["translator2_id"] = $translator2_id;
            $template_info["topic1_id"] = $topic1_id;
            $template_info["topic2_id"] = $topic2_id;
            $template_info["topic3_id"] = $topic3_id;
            $template_info["topic4_id"] = $topic4_id;
            $template_info["topic5_id"] = $topic5_id;
            $template_info["cycle_zh"] = $cycle_zh;
            $template_info["cycle_ru"] = $cycle_ru;
            $template_info["corder"] = $corder;
            $template_info["subcycle_zh"] = $subcycle_zh;
            $template_info["subcycle_ru"] = $subcycle_ru;
            $template_info["scorder"] = $scorder;
            $template_info["poem_name_zh"] = $poem_name_zh;
            $template_info["poem_name_ru"] = $poem_name_ru;
            $template_info["poem_code"] = $poem_code;
            $template_info["biblio_id"] = $biblio_id;
            $template_info["poem_text"] = $poem_text;
            $template_info["totallines"] = $totallines;
            $template_info["fulllines"] = $fulllines;
            $template_info["genres"] = $genres;
            $template_info["site"] = $site;
            $template_info["siteURL"] = $siteURL;
            $error = false;
            $template_info["error"] = $error;
            $template_info["success"] = false;
        }
        $template = $twig->load('poem_modify_form.html.twig');
    } else {
        $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
         и подставьте в URL.';
        $template_info["error"] = $error;
        $template_info["success"] = false;
    }
    $template = $twig->load('poem_modify_form.html.twig');
}

//    $template = $twig->load('admin_authors.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);
