<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='News';
if (array_key_exists('action', $_GET)) { 
    $records = array();
    if ($_GET['action'] == 'showall') {
        $records = getAllNews();
		$template_info["showDataTable"] = true;
		$template_info["records"] = $records;
		$template = $twig->load('admin_news_showall.html.twig');
    }
    if ($_GET['action'] == 'modify') {
        if (preg_match("/^\d+$/", $_GET['record_id'])) {
            $record_id = $_GET['record_id'];			
            $template_info["modify"] = true;
            $template_info["insert"] = false;
            if ((array_key_exists('posted', $_GET))  && (!empty($_POST))){
                if (array_key_exists('header', $_POST)) {
                    $header = (!empty($_POST['header'])) ? $_POST['header'] : NULL;
                }
                if (array_key_exists('text', $_POST)) {
                    $text = (!empty($_POST['text'])) ? $_POST['text'] : NULL;
                }
                $template_info["record_id"] = $record_id;
                $template_info["header"] = $header;
                $template_info["text"] = $text;
                $r_id = updateNewsByID($record_id, $header, $text);
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
                $record = getByIDFromNews($_GET['record_id']);
                list($id, $header, $text, $dt) = $record;
                $template_info["record_id"] = $id;
                $template_info["header"] = $header;
                $template_info["text"] = $text;
                $error = false;
                $template_info["error"] = $error;
                $template_info["success"] = false;
            }
            $template = $twig->load('admin_news_modify.html.twig');
            
        } else {
            $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
            в Aвторы->Все авторы и подставьте в URL.';
            $template_info["error"] = $error;
            $template_info["success"] = false;
            $template = $twig->load('blank.html.twig');
        }
    }
	if ($_GET['action'] == 'insert') {
        $template_info["insert"] = true;
        $template_info["modify"] = false;
		if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
		}
		else {
			$_POST = array_map("trim",$_POST);
			$error = false;
			$success = false;
            if (array_key_exists('header', $_POST)) {
                $header = (!empty($_POST['header'])) ? $_POST['header'] : NULL;
            }
            if (array_key_exists('text', $_POST)) {
                $text = (!empty($_POST['text'])) ? $_POST['text'] : NULL;
            }
            $template_info["header"] = $header;
            $template_info["text"] = $text;
            $r_id = news_insert_record($header, $text);
			if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
		}
        $template = $twig->load('admin_news_modify.html.twig');
	}
}
else {
    $template_info["content"] ='Requested page does not exist. Contact site admin. ';
    $template = $twig->load('page.html.twig');
}
echo $template->display($template_info);
    