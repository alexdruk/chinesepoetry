<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Источники';
if (array_key_exists('action', $_GET)) { 
	$records = array();
	$records = getAllFromSources();
	$template_info["showDataTable"] = false;
	if ($_GET['action'] == 'showall') {
		$template_info["showDataTable"] = true;
		$template_info["records"] = $records;
		$template = $twig->load('admin_sources_showall.html.twig');
	}
	if ($_GET['action'] == 'showlast') {
		$records = array_slice($records,-1,1);
		$template_info["records"] = $records;
		$template = $twig->load('admin_sources_showall.html.twig');
	}
	if ($_GET['action'] == 'insert') {
		if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('sources_insert_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
			$error = false;
			$success = false;
            if (array_key_exists('author', $_POST)) {
                $author = (!empty($_POST['author'])) ? $_POST['author'] : NULL;
            }
            if (array_key_exists('translator', $_POST)) {
                $translator = (!empty($_POST['translator'])) ? $_POST['translator'] : NULL;
            }
            if (array_key_exists('seria', $_POST)) {
                $seria = (!empty($_POST['seria'])) ? $_POST['seria'] : NULL;
            }
            if (array_key_exists('publisher', $_POST)) {
                $publisher = (!empty($_POST['publisher'])) ? $_POST['publisher'] : NULL;
            }
            if (array_key_exists('code', $_POST)) {
				$code = (!empty($_POST['code'])) ? $_POST['code'] : NULL;
			}
            if (array_key_exists('ISBN', $_POST)) {
                $ISBN = (!empty($_POST['ISBN'])) ? $_POST['ISBN'] : NULL;
            }
			$book_name = $_POST['book_name'];
			$ref_name = $_POST['ref_name'];
			$year = $_POST['year'];
			$in_antology = $_POST['in_antology'];
			$biblio_name = $_POST['biblio_name'];
			$r_id = sources_insert_record($author, $book_name, $translator, $ref_name, $seria, $publisher, $code, $year, $ISBN, $in_antology, $biblio_name);
			if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
			$template = $twig->load('sources_insert_form.html.twig');
		}
	}
	if ($_GET['action'] == 'modify') {
		if (preg_match("/^\d+$/", $_GET['record_id'])) {			
			// if POST present 
			if ((array_key_exists('posted', $_GET))  && (!empty($_POST))){
#print_r ($_POST);
				$biblio_id = $_GET['record_id'];
				if (array_key_exists('author', $_POST)) {
					$author = (!empty($_POST['author'])) ? $_POST['author'] : NULL;
				}
				if (array_key_exists('book_name', $_POST)) {
					$book_name = (!empty($_POST['book_name'])) ? $_POST['book_name'] : NULL;
				}
				if (array_key_exists('translator', $_POST)) {
					$translator = (!empty($_POST['translator'])) ? $_POST['translator'] : NULL;
				}
				if (array_key_exists('ref_name', $_POST)) {
					$ref_name = (!empty($_POST['ref_name'])) ? $_POST['ref_name'] : NULL;
				}
				if (array_key_exists('seria', $_POST)) {
					$seria = (!empty($_POST['seria'])) ? $_POST['seria'] : NULL;
				}
				if (array_key_exists('publisher', $_POST)) {
					$publisher = (!empty($_POST['publisher'])) ? $_POST['publisher'] : NULL;
				}
				if (array_key_exists('year', $_POST)) {
					$year = (!empty($_POST['year'])) ? $_POST['year'] : NULL;
				}
				if (array_key_exists('code', $_POST)) {
					$code = (!empty($_POST['code'])) ? $_POST['code'] : NULL;
				}
				if (array_key_exists('biblio_name', $_POST)) {
					$biblio_name = (!empty($_POST['biblio_name'])) ? $_POST['biblio_name'] : NULL;
				}
				if (array_key_exists('ISBN', $_POST)) {
					$ISBN = (!empty($_POST['ISBN'])) ? $_POST['ISBN'] : NULL;
				}
				$present = $_POST['present'];
#				if (array_key_exists('present', $_POST)) {
#					$present = (!empty($_POST['present'])) ? $_POST['present'] : 0;
#				}
				$template_info["record_id"] = $biblio_id;
				$template_info["author"] = $author;
				$template_info["book_name"] = $book_name;
				$template_info["translator"] = $translator;
				$template_info["ref_name"] = $ref_name;
				$template_info["seria"] = $seria;
				$template_info["publisher"] = $publisher;
				$template_info["year"] = $year;
				$template_info["code"] = $code;
				$template_info["biblio_name"] = $biblio_name;
				$template_info["ISBN"] = $ISBN;
				$template_info["present"] = $present;
				$r_id = updateSourcesByID($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present);
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
				$record = getByIDFromSources($_GET['record_id']);
				list($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present) = $record;
#print_r($record);
				$template_info["record_id"] = $biblio_id;
				$template_info["author"] = $author;
				$template_info["book_name"] = $book_name;
				$template_info["translator"] = $translator;
				$template_info["ref_name"] = $ref_name;
				$template_info["seria"] = $seria;
				$template_info["publisher"] = $publisher;
				$template_info["year"] = $year;
				$template_info["code"] = $code;
				$template_info["biblio_name"] = $biblio_name;
				$template_info["ISBN"] = $ISBN;
				$template_info["present"] = $present;
				$error = false;
				$template_info["error"] = $error;
				$template_info["success"] = false;
				}
		} else {
			$error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
			 в Источники->Все записи и подставьте в URL.';
			$template_info["error"] = $error;
			$template_info["success"] = false;
		}
		$template = $twig->load('sources_modify_form.html.twig');
	}
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);

