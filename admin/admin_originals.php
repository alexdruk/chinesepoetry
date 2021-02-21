<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Оригиналы';
if (array_key_exists('action', $_GET)) { 
    $records = array();
    if ($_GET['action'] == 'showall') {
        $template_info["header"] ='Все оригиналы';
        $template_info["showDataTable"] = true;
        $records = getListfromOriginals();       
		$template_info["records"] = $records;
        $template = $twig->load('admin_originals_showall.html.twig');
    }
    elseif ($_GET['action'] == 'insert') {
        $template_info["header"] ='Добавить оригинал';
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('originals_insert_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
#			$_POST = array_map("addslashes",$_POST); 			
			$error = false;
			$success = false;
            $author_id = $_POST['author_id'];
            $cycle_zh = $_POST['cycle_zh'];
            $cycle_ru = $_POST['cycle_ru'];
            $subcycle_zh = $_POST['subcycle_zh']; 
            $subcycle_ru = $_POST['subcycle_ru']; 
            $poem_name_zh = $_POST['poem_name_zh'];
            $poem_name_ru = $_POST['poem_name_ru'];
            $biblio_id = $_POST['biblio_id'];
            $poem_code = $_POST['poem_code'];
            $poem_text = $_POST['poem_text'];
            $genres  = $_POST['genres'];
            $size  = $_POST['size'];
            $site  = $_POST['site'];
            $siteURL  = $_POST['siteURL'];
			$r_id = originals_insert_record($author_id, $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_name_zh, $poem_name_ru, $poem_code, $biblio_id, $poem_text, $genres, $size, $site, $siteURL);
            if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
			$template = $twig->load('originals_insert_form.html.twig');
		}
    }
   if ($_GET['action'] == 'modify') {
    if (preg_match("/^\d+$/", $_GET['record_id'])) {			
        // if POST present 
        if ((array_key_exists('posted', $_GET))  && (!empty($_POST))){
            $originals_id = $_GET['record_id'];
            if (array_key_exists('cycle_zh', $_POST)) {
                $cycle_zh = (!empty($_POST['cycle_zh'])) ? $_POST['cycle_zh'] : NULL;
            }
            if (array_key_exists('cycle_ru', $_POST)) {
                $cycle_ru = (!empty($_POST['cycle_ru'])) ? $_POST['cycle_ru'] : NULL;
            }
            if (array_key_exists('subcycle_zh', $_POST)) {
                $subcycle_zh = (!empty($_POST['subcycle_zh'])) ? $_POST['subcycle_zh'] : NULL;
            }
            if (array_key_exists('subcycle_ru', $_POST)) {
                $subcycle_ru = (!empty($_POST['subcycle_ru'])) ? $_POST['subcycle_ru'] : NULL;
            }
            if (array_key_exists('biblio_id', $_POST)) {
                $biblio_id = (!empty($_POST['biblio_id'])) ? $_POST['biblio_id'] : NULL;
            }
            if (array_key_exists('poem_code', $_POST)) {
                $poem_code = (!empty($_POST['poem_code'])) ? $_POST['poem_code'] : NULL;
            }
            if (array_key_exists('poem_name_zh', $_POST)) {
                $poem_name_zh = (!empty($_POST['poem_name_zh'])) ? $_POST['poem_name_zh'] : NULL;
            }
            if (array_key_exists('poem_name_ru', $_POST)) {
                $poem_name_ru = (!empty($_POST['poem_name_ru'])) ? $_POST['poem_name_ru'] : NULL;
            }
            if (array_key_exists('poem_text', $_POST)) {
                $poem_text = (!empty($_POST['poem_text'])) ? $_POST['poem_text'] : NULL;
            }
            if (array_key_exists('genres', $_POST)) {
                $genres = (!empty($_POST['genres'])) ? $_POST['genres'] : NULL;
            }
            if (array_key_exists('size', $_POST)) {
                $size = (!empty($_POST['size'])) ? $_POST['size'] : NULL;
            }
            if (array_key_exists('site', $_POST)) {
                $site = (!empty($_POST['site'])) ? $_POST['site'] : NULL;
            }
            if (array_key_exists('siteURL', $_POST)) {
                $siteURL = (!empty($_POST['siteURL'])) ? $_POST['siteURL'] : NULL;
            }
            $template_info["header"] = 'Редактировать оригинал';
            $template_info["record_id"] = $originals_id;
            $template_info["cycle_zh"] = $cycle_zh;
            $template_info["cycle_ru"] = $cycle_ru;
            $template_info["subcycle_zh"] = $subcycle_zh;
            $template_info["subcycle_ru"] = $subcycle_ru;
            $template_info["poem_name_zh"] = $poem_name_zh;
            $template_info["poem_name_ru"] = $poem_name_ru;
            $template_info["biblio_id"] = $biblio_id;
            $template_info["poem_code"] = $poem_code;
            $template_info["poem_text"] = $poem_text;
            $template_info["genres"] = $genres;
            $template_info["size"] = $size;
            $template_info["site"] = $site;
            $template_info["siteURL"] = $siteURL;
            $r_id = updateOriginalPoemByID($originals_id, $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $biblio_id, $poem_code, $poem_name_zh,  $poem_name_ru, $poem_text, $genres, $size, $site, $siteURL);
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
            $record = getOriginalsByPoemID($_GET['record_id']);
            list($originals_id,$author_id,$proper_name, $dates,$epoch,$cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru,$poem_name_zh, $poem_name_ru,$poem_code,$biblio_id,$poem_text, $genres, $size, $site, $siteURL) = $record;
            $template_info["header"] = 'Редактировать оригинал';
            $author = $proper_name.' '.$dates;
            $template_info["record_id"] = $originals_id;
            $template_info["cycle_zh"] = $cycle_zh;
            $template_info["cycle_ru"] = $cycle_ru;
            $template_info["subcycle_zh"] = $subcycle_zh;
            $template_info["subcycle_ru"] = $subcycle_ru;
            $template_info["poem_name_zh"] = $poem_name_zh;
            $template_info["poem_name_ru"] = $poem_name_ru;
            $template_info["biblio_id"] = $biblio_id;
            $template_info["poem_code"] = $poem_code;
            $template_info["poem_text"] = $poem_text;
            $template_info["genres"] = $genres;
            $template_info["size"] = $size;
            $template_info["site"] = $site;
            $template_info["siteURL"] = $siteURL;
            $error = false;
            $template_info["error"] = $error;
            $template_info["success"] = false;
        }
        $template = $twig->load('originals_modify_form.html.twig');
    } else {
        $template_info["header"] = 'В URL не указан record_id.';
        $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
         в Оригиналы->Все оригиналы и подставьте в URL.';
        $template_info["error"] = $error;
        $template_info["success"] = false;
    }
    $template = $twig->load('originals_modify_form.html.twig');
}

//    $template = $twig->load('admin_authors.html.twig');
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);
