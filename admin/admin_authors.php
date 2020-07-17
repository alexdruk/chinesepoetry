<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Авторы';
if (array_key_exists('action', $_GET)) { 
    $records = array();
    if ($_GET['action'] == 'showall') {
        $template_info["header"] ='Все авторы';
        $template_info["showDataTable"] = true;
        $records = getAllfromAuthors();       
		$template_info["records"] = $records;
        $template = $twig->load('admin_authors_showall.html.twig');
    }
    elseif ($_GET['action'] == 'insert') {
        $template_info["header"] ='Добавить автора';
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('authors_insert_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
#			$_POST = array_map("addslashes",$_POST); 			
			$error = false;
			$success = false;
            $full_name = $_POST['full_name'];
            $proper_name = $_POST['proper_name'];
            $dates = $_POST['dates']; 
            $epoch = $_POST['epoch']; 
            $present = $_POST['present'];
			$r_id = authors_insert_record($full_name, $proper_name,  $dates,  $epoch, $present);
			if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
			$template = $twig->load('authors_insert_form.html.twig');
		}
    }
    elseif ($_GET['action'] == 'insertfile') {
        $template_info["header"] ='Добавить файл  автора';
        $template_info["insertfile"] = true;           
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('authors_insertfile_form.html.twig');
		}
		else {
            if (preg_match("/^\d+$/", $_POST['author_id'])) {
                $author_id = $_POST['author_id'];
                $desc = $_POST['description'];
                
                $r_id = authors_insert_description($author_id, $desc);
                if ($r_id > 0) {
                    $success = 'Success! A '.$r_id.' record was updated';
                    $template_info["success"] = $success;
                    $template_info["error"] = false;
                    }
                else {
                    $error = 'An error occur! See DB log files.';
                    $template_info["success"] = false;
                    $template_info["error"] = $error;
                }
             }
            else {
                $error = 'Автор id должен быть цифра';
                $template_info["error"] = $error;
                $template_info["success"] = false;
            }
            $template = $twig->load('authors_insertfile_form.html.twig');
        }
    }
    elseif ($_GET['action'] == 'modifyfile') {
        $template_info["header"] ='Редактировать биографию автора';
        $template_info["insertfile"] = false;           
        if (preg_match("/^\d+$/", $_GET['record_id'])) {
            $record_id = $_GET['record_id'];
            list($proper_name, $dates, $epoch, $doc_text) = getDocByIDFromAuthors($record_id);           
            $template_info["author_id"] = $record_id;
            $template_info["desc"] = $doc_text;
            if (empty($_POST)) {
                $template_info["success"] = false;
                $template_info["error"] = false;
                $template = $twig->load('authors_insertfile_form.html.twig');
            }
            else {
                if (preg_match("/^\d+$/", $_POST['author_id'])) {
                    $author_id = $_POST['author_id'];
                    $desc = $_POST['description'];
                    
                    $r_id = authors_modify_description($author_id, $desc);
                    if ($r_id > 0) {
                        $success = 'Success! A '.$r_id.' record was inserted';
                        $template_info["success"] = $success;
                        $template_info["error"] = false;
                        }
                    else {
                        $error = 'An error occur! See DB log files.';
                        $template_info["success"] = false;
                        $template_info["error"] = $error;
                        $template = $twig->load('blank.html.twig');
                    }
                }
            }
            $template = $twig->load('authors_insertfile_form.html.twig');
        } else {
            $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
            в Aвторы->Все авторы и подставьте в URL.';
            $template_info["error"] = $error;
            $template_info["success"] = false;
			$template = $twig->load('blank.html.twig');
        }
    }

    if ($_GET['action'] == 'modify') {
    if (preg_match("/^\d+$/", $_GET['record_id'])) {			
        // if POST present 
        if ((array_key_exists('posted', $_GET))  && (!empty($_POST))){
            $author_id = $_GET['record_id'];
            if (array_key_exists('full_name', $_POST)) {
                $full_name = (!empty($_POST['full_name'])) ? $_POST['full_name'] : NULL;
            }
            if (array_key_exists('proper_name', $_POST)) {
                $proper_name = (!empty($_POST['proper_name'])) ? $_POST['proper_name'] : NULL;
            }
            if (array_key_exists('dates', $_POST)) {
                $dates = (!empty($_POST['dates'])) ? $_POST['dates'] : NULL;
            }
            if (array_key_exists('epoch', $_POST)) {
                $epoch = (!empty($_POST['epoch'])) ? $_POST['epoch'] : NULL;
            }
            if (array_key_exists('present', $_POST)) {
                $present = (!empty($_POST['present'])) ? $_POST['present'] : NULL;
            }
            $template_info["record_id"] = $author_id;
            $template_info["full_name"] = $full_name;
            $template_info["proper_name"] = $proper_name;
            $template_info["dates"] = $dates;
            $template_info["epoch"] = $epoch;
            $template_info["present"] = $present;
            $r_id = updateAuthorsByID($author_id, $full_name, $proper_name, $dates, $epoch, $present);
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
            $record = getByIDFromAuthors($_GET['record_id']);
            list($author_id, $full_name, $proper_name,  $dates, $epoch, $present) = $record;
            $template_info["record_id"] = $author_id;
            $template_info["full_name"] = $full_name;
            $template_info["proper_name"] = $proper_name;
            $template_info["dates"] = $dates;
            $template_info["epoch"] = $epoch;
            $template_info["present"] = $present;
            $error = false;
            $template_info["error"] = $error;
            $template_info["success"] = false;
        }
        $template = $twig->load('authors_modify_form.html.twig');
    } else {
        $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
         в Aвторы->Все авторы и подставьте в URL.';
        $template_info["error"] = $error;
        $template_info["success"] = false;
        $template = $twig->load('blank.html.twig');
        }
    }
    if ($_GET['action'] == 'insertAtributes') {
        $template_info["header"] ='Добавить атрибуты авторов';
        $template_info["insert"] = true;
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('atrib_insert.html.twig');
		}
		else {
            if (preg_match("/^\d+$/", $_POST['author_id'])) {
                $_POST = array_map("trim",$_POST);
#                $_POST = array_map("addslashes",$_POST); 			
                $author_id = $_POST['author_id'];
                $error = false;
                $success = false;
                $palladian = (!empty($_POST['palladian'])) ? $_POST['palladian'] : NULL;
                $zh_trad = (!empty($_POST['zh_trad'])) ? $_POST['zh_trad'] : NULL;
                $zh_simple = (!empty($_POST['zh_simple'])) ? $_POST['zh_simple'] : NULL;
                $pinyin = (!empty($_POST['pinyin'])) ? $_POST['pinyin'] : NULL;
                $real_name = (!empty($_POST['real_name'])) ? $_POST['real_name'] : NULL;
                $real_name_zh = (!empty($_POST['real_name_zh'])) ? $_POST['real_name_zh'] : NULL;
                $real_name_simple = (!empty($_POST['real_name_simple'])) ? $_POST['real_name_simple'] : NULL;
                $real_name_pinyin = (!empty($_POST['real_name_pinyin'])) ? $_POST['real_name_pinyin'] : NULL;
                $second_name = (!empty($_POST['second_name'])) ? $_POST['second_name'] : NULL;
                $second_name_zh = (!empty($_POST['second_name_zh'])) ? $_POST['second_name_zh'] : NULL;
                $second_name_simple = (!empty($_POST['second_name_simple'])) ? $_POST['second_name_simple'] : NULL;
                $second_name_pinyin = (!empty($_POST['second_name_pinyin'])) ? $_POST['second_name_pinyin'] : NULL;
                $postmortem_name = (!empty($_POST['postmortem_name'])) ? $_POST['postmortem_name'] : NULL;
                $postmortem_name_zh = (!empty($_POST['postmortem_name_zh'])) ? $_POST['postmortem_name_zh'] : NULL;
                $postmortem_name_simple = (!empty($_POST['postmortem_name_simple'])) ? $_POST['postmortem_name_simple'] : NULL;
                $postmortem_name_pinyin = (!empty($_POST['postmortem_name_pinyin'])) ? $_POST['postmortem_name_pinyin'] : NULL;
                $pseudonim_name = (!empty($_POST['pseudonim_name'])) ? $_POST['pseudonim_name'] : NULL;
                $pseudonim_name_zh = (!empty($_POST['pseudonim_name_zh'])) ? $_POST['pseudonim_name_zh'] : NULL;
                $pseudonim_name_simple = (!empty($_POST['pseudonim_name_simple'])) ? $_POST['pseudonim_name_simple'] : NULL;
                $pseudonim_name_pinyin = (!empty($_POST['pseudonim_name_pinyin'])) ? $_POST['pseudonim_name_pinyin'] : NULL;
                $nickname = (!empty($_POST['nickname'])) ? $_POST['nickname'] : NULL;
                $nickname_zh = (!empty($_POST['nickname_zh'])) ? $_POST['nickname_zh'] : NULL;
                $nickname_simple = (!empty($_POST['nickname_simple'])) ? $_POST['nickname_simple'] : NULL;
                $nickname_pinyin = (!empty($_POST['nickname_pinyin'])) ? $_POST['nickname_pinyin'] : NULL;
                $r_id = authors_insert_atrib($author_id, $palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,
                $real_name_simple,$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,
                $postmortem_name,$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,
                $pseudonim_name,$pseudonim_name_zh,$pseudonim_name_simple,$pseudonim_name_pinyin,
                $nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,$forsearch);
                if ($r_id > 0) {
                    $success = 'Success! A new record was created id='.$r_id;
                }
                else {
                    $error = 'An error occur! See DB log files.';
                }
                $template_info["error"] = $error;
                $template_info["success"] = $success;
                $template = $twig->load('atrib_insert.html.twig');
            }
        }
     }
     if ($_GET['action'] == 'modifyAtributes') {
        $template_info["header"] ='Редактировать атрибуты авторов';
        $template_info["insert"] = false;
        if (preg_match("/^\d+$/", $_GET['record_id'])) {
            $_POST = array_map("trim",$_POST);
#            $_POST = array_map("addslashes",$_POST); 			
            $author_id = $_GET['record_id'];
            $error = false;
            $success = false;
            $atribs = getAtributesByAuthorID($author_id);
            $template_info["atribs"] = $atribs;
            if (empty($_POST)) {
                $template_info["success"] = false;
                $template_info["error"] = false;
                $template = $twig->load('atrib_insert.html.twig');
            }
            else {    
                $forsearch = $_POST['forsearch'];
                $palladian = (!empty($_POST['palladian'])) ? $_POST['palladian'] : NULL;
                $zh_trad = (!empty($_POST['zh_trad'])) ? $_POST['zh_trad'] : NULL;
                $zh_simple = (!empty($_POST['zh_simple'])) ? $_POST['zh_simple'] : NULL;
                $pinyin = (!empty($_POST['pinyin'])) ? $_POST['pinyin'] : NULL;
                $real_name = (!empty($_POST['real_name'])) ? $_POST['real_name'] : NULL;
                $real_name_zh = (!empty($_POST['real_name_zh'])) ? $_POST['real_name_zh'] : NULL;
                $real_name_simple = (!empty($_POST['real_name_simple'])) ? $_POST['real_name_simple'] : NULL;
                $real_name_pinyin = (!empty($_POST['real_name_pinyin'])) ? $_POST['real_name_pinyin'] : NULL;
                $second_name = (!empty($_POST['second_name'])) ? $_POST['second_name'] : NULL;
                $second_name_zh = (!empty($_POST['second_name_zh'])) ? $_POST['second_name_zh'] : NULL;
                $second_name_simple = (!empty($_POST['second_name_simple'])) ? $_POST['second_name_simple'] : NULL;
                $second_name_pinyin = (!empty($_POST['second_name_pinyin'])) ? $_POST['second_name_pinyin'] : NULL;
                $postmortem_name = (!empty($_POST['postmortem_name'])) ? $_POST['postmortem_name'] : NULL;
                $postmortem_name_zh = (!empty($_POST['postmortem_name_zh'])) ? $_POST['postmortem_name_zh'] : NULL;
                $postmortem_name_simple = (!empty($_POST['postmortem_name_simple'])) ? $_POST['postmortem_name_simple'] : NULL;
                $postmortem_name_pinyin = (!empty($_POST['postmortem_name_pinyin'])) ? $_POST['postmortem_name_pinyin'] : NULL;
                $pseudonim_name = (!empty($_POST['pseudonim_name'])) ? $_POST['pseudonim_name'] : NULL;
                $pseudonim_name_zh = (!empty($_POST['pseudonim_name_zh'])) ? $_POST['pseudonim_name_zh'] : NULL;
                $pseudonim_name_simple = (!empty($_POST['pseudonim_name_simple'])) ? $_POST['pseudonim_name_simple'] : NULL;
                $pseudonim_name_pinyin = (!empty($_POST['pseudonim_name_pinyin'])) ? $_POST['pseudonim_name_pinyin'] : NULL;
                $nickname = (!empty($_POST['nickname'])) ? $_POST['nickname'] : NULL;
                $nickname_zh = (!empty($_POST['nickname_zh'])) ? $_POST['nickname_zh'] : NULL;
                $nickname_simple = (!empty($_POST['nickname_simple'])) ? $_POST['nickname_simple'] : NULL;
                $nickname_pinyin = (!empty($_POST['nickname_pinyin'])) ? $_POST['nickname_pinyin'] : NULL;
                $r_id = authors_modify_atrib($author_id, $palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,
                $real_name_simple,$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,
                $postmortem_name,$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,
                $pseudonim_name,$pseudonim_name_zh,$pseudonim_name_simple,$pseudonim_name_pinyin,
                $nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,$forsearch);
                if ($r_id > 0) {
                    $success = 'Success! A new record was created id='.$r_id;
                }
                else {
                    $error = 'An error occur! See DB log files.';
                }
                $template_info["error"] = $error;
                $template_info["success"] = $success;
            }
            $template = $twig->load('atrib_insert.html.twig');
        } else {
            $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
            в Aвторы->Все авторы и подставьте в URL.';
            $template_info["error"] = $error;
            $template_info["success"] = false;
            $template = $twig->load('blank.html.twig');
        }
    }
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);
