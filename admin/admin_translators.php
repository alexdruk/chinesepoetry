<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Переводчики';
if (array_key_exists('action', $_GET)) { 
    $records = array();
    if ($_GET['action'] == 'showall') {
        $template_info["header"] ='Все переводчики';
        $template_info["showDataTable"] = true;
        $records = getAllfromTranslators();       
		$template_info["records"] = $records;
        $template = $twig->load('admin_translators_showall.html.twig');
    }
    elseif ($_GET['action'] == 'insert') {
        $template_info["header"] ='Добавить переводчика';
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('translators_insert_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
#			$_POST = array_map("addslashes",$_POST); 			
			$error = false;
			$success = false;
            $full_name = $_POST['full_name'];
            $lit_name = $_POST['lit_name'];
            $real_name = $_POST['real_name']; 
            $first_name = $_POST['first_name']; 
            $father_name = $_POST['father_name']; 
            $pseudonyms = $_POST['pseudonyms']; 
            $born = $_POST['born']; 
            $born_place = $_POST['born_place']; 
            $died = $_POST['died']; 
            $died_place = $_POST['died_place']; 
            $present = $_POST['present'];
			$r_id = translators_insert_record($full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present);
			if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
			$template = $twig->load('translators_insert_form.html.twig');
		}
    }
    elseif ($_GET['action'] == 'insertfile') {
        $template_info["header"] ='Добавить файл  переводчика';
        $template_info["insertfile"] = true;
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('translators_insertfile_form.html.twig');
		}
		else {
            if (preg_match("/^\d+$/", $_POST['translator_id'])) {
                $translator_id = $_POST['translator_id'];
                $doc_text = $_POST['description'];
                if (array_key_exists('full_name', $_POST)) {
                    $full_name = (!empty($_POST['full_name'])) ? $_POST['full_name'] : NULL;
                }
                if (array_key_exists('dates', $_POST)) {
                    $dates = (!empty($_POST['dates'])) ? $_POST['dates'] : NULL;
                }
                if (array_key_exists('img', $_POST)) {
                    $img = (!empty($_POST['img'])) ? $_POST['img'] : NULL;
                }
                if (array_key_exists('summary', $_POST)) {
                    $summary = (!empty($_POST['summary'])) ? $_POST['summary'] : NULL;
                }
                if (array_key_exists('description', $_POST)) {
                    $doc_text = (!empty($_POST['description'])) ? $_POST['description'] : NULL;
                }
            $r_id = translators_insert_description($translator_id, $full_name, $dates, $img, $summary, $doc_text);
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
            $template = $twig->load('translators_insertfile_form.html.twig');
        }
    }
    elseif ($_GET['action'] == 'modifyfile') {
        $template_info["header"] ='Редактировать биографию переводчика';
        $template_info["insertfile"] = false;           
        if (preg_match("/^\d+$/", $_GET['record_id'])) {
            $record_id = $_GET['record_id'];
            list($id,$translator_id,$full_name,$dates,$summary,$img,$doc_text) = getTranslatorDescByID($record_id) ;           
            $template_info["record_id"] = $id;
            $template_info["translator_id"] = $translator_id;
            if (empty($_POST)) {
                $template_info["success"] = false;
                $template_info["error"] = false;
                $template_info["translator_id"] = $translator_id;
                $template_info["full_name"] = $full_name;
                $template_info["dates"] = $dates;
                $template_info["summary"] = $summary;
                $template_info["img"] = $img;
                $template_info["doc_text"] = $doc_text;
                $template = $twig->load('translators_insertfile_form.html.twig');
            }
            else {
                    $record_id = $_GET['record_id'];
                    $translator_id = $_POST['translator_id'];
                    if (array_key_exists('full_name', $_POST)) {
                        $full_name = (!empty($_POST['full_name'])) ? $_POST['full_name'] : NULL;
                    }
                    if (array_key_exists('dates', $_POST)) {
                        $dates = (!empty($_POST['dates'])) ? $_POST['dates'] : NULL;
                    }
                    if (array_key_exists('img', $_POST)) {
                        $img = (!empty($_POST['img'])) ? $_POST['img'] : NULL;
                    }
                    if (array_key_exists('summary', $_POST)) {
                        $summary = (!empty($_POST['summary'])) ? $_POST['summary'] : NULL;
                    }
                    if (array_key_exists('description', $_POST)) {
                        $doc_text = (!empty($_POST['description'])) ? $_POST['description'] : NULL;
                    }
                    $template_info["record_id"] = $record_id;
                    $template_info["translator_id"] = $translator_id;
                    $template_info["full_name"] = $full_name;
                    $template_info["dates"] = $dates;
                    $template_info["summary"] = $summary;
                    $template_info["img"] = $img;
                    $template_info["doc_text"] = $doc_text;
                     
                    $r_id = translators_modify_description($translator_id, $full_name, $dates, $summary, $img, $doc_text);
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
            $template = $twig->load('translators_insertfile_form.html.twig');
        } else {
            $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
            в Переводчики->Все Переводчики и подставьте в URL.';
            $template_info["error"] = $error;
            $template_info["success"] = false;
			$template = $twig->load('blank.html.twig');
        }
    }
   if ($_GET['action'] == 'modify') {
    $template_info["header"] ='Редактировать переводчика';
    if (preg_match("/^\d+$/", $_GET['record_id'])) {			
        // if POST present 
        if ((array_key_exists('posted', $_GET))  && (!empty($_POST))){
            $translator_id = $_GET['record_id'];
            if (array_key_exists('full_name', $_POST)) {
                $full_name = (!empty($_POST['full_name'])) ? $_POST['full_name'] : NULL;
            }
            if (array_key_exists('lit_name', $_POST)) {
                $lit_name = (!empty($_POST['lit_name'])) ? $_POST['lit_name'] : NULL;
            }
            if (array_key_exists('real_name', $_POST)) {
                $real_name = (!empty($_POST['real_name'])) ? $_POST['real_name'] : NULL;
            }
            if (array_key_exists('first_name', $_POST)) {
                $first_name = (!empty($_POST['first_name'])) ? $_POST['first_name'] : NULL;
            }
            if (array_key_exists('father_name', $_POST)) {
                $father_name = (!empty($_POST['father_name'])) ? $_POST['father_name'] : NULL;
            }
            if (array_key_exists('pseudonyms', $_POST)) {
                $pseudonyms = (!empty($_POST['pseudonyms'])) ? $_POST['pseudonyms'] : NULL;
            }
            if (array_key_exists('born', $_POST)) {
                $born = (!empty($_POST['born'])) ? $_POST['born'] : NULL;
            }
            if (array_key_exists('born_place', $_POST)) {
                $born_place = (!empty($_POST['born_place'])) ? $_POST['born_place'] : NULL;
            }
            if (array_key_exists('died', $_POST)) {
                $died = (!empty($_POST['died'])) ? $_POST['died'] : NULL;
            }
            if (array_key_exists('died_place', $_POST)) {
                $died_place = (!empty($_POST['died_place'])) ? $_POST['died_place'] : NULL;
            }
            if (array_key_exists('present', $_POST)) {
                $present = (!empty($_POST['present'])) ? $_POST['present'] : NULL;
            }
            $template_info["record_id"] = $translator_id;
            $template_info["full_name"] = $full_name;
            $template_info["lit_name"] = $lit_name;
            $template_info["real_name"] = $real_name;
            $template_info["first_name"] = $first_name;
            $template_info["father_name"] = $father_name;
            $template_info["pseudonyms"] = $pseudonyms;
            $template_info["born"] = $born;
            $template_info["born_place"] = $born_place;
            $template_info["died"] = $died;
            $template_info["died_place"] = $died_place;
            $template_info["present"] = $present;
            $r_id = updateTranslatorsByID($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present);
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
            $record = getByIDFromTranslators($_GET['record_id']);
            list($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present) = $record;
            $template_info["record_id"] = $translator_id;
            $template_info["full_name"] = $full_name;
            $template_info["lit_name"] = $lit_name;
            $template_info["real_name"] = $real_name;
            $template_info["first_name"] = $first_name;
            $template_info["father_name"] = $father_name;
            $template_info["pseudonyms"] = $pseudonyms;
            $template_info["born"] = $born;
            $template_info["born_place"] = $born_place;
            $template_info["died"] = $died;
            $template_info["died_place"] = $died_place;
            $template_info["present"] = $present;
            $error = false;
            $template_info["error"] = $error;
            $template_info["success"] = false;
        }
        $template = $twig->load('translators_modify_form.html.twig');
    } else {
        $error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
         в Переводчики->Все переводчики и подставьте в URL.';
        $template_info["error"] = $error;
        $template_info["success"] = false;
    }
    $template = $twig->load('translators_modify_form.html.twig');
}

}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);
