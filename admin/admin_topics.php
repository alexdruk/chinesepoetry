<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='admin home page';
if (array_key_exists('action', $_GET)) { 
	$records = array();
	$records = getAllFromTopics();
	if ($_GET['action'] == 'showall') {
		$template_info["records"] = $records;
		$template = $twig->load('admin_topics_showall.html.twig');
	}
	if ($_GET['action'] == 'addtopic') {
		if (empty($_POST)) {
			$template_info["error"] = false;
			$template_info["success"] = false;
			$template = $twig->load('topics_insert_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
			$_POST = array_map("addslashes",$_POST); 
			$err = NULL;
			$success = NULL;
			if ( !isset($_POST['topicName'] )){
				$err = 'some paramets are wrong or missing';
            }
            $topic_name = NULL; 
            $synonym = NULL;
            $present = NULL;
            $template_info["error"] = $err;
            if (array_key_exists('topicName', $_POST)) {
                $topic_name = $_POST['topicName'];
            }
            if (array_key_exists('topicSynonym', $_POST)) {
                $synonym = $_POST['topicSynonym'];
            }
            if (array_key_exists('inputPresent', $_POST)) {
                $present = $_POST['inputPresent'];
            }
			$r_id = topics_insert_record($topic_name, $synonym, $present);
			if ($r_id > 0) {
				$success = 'success!';
			}
			$template_info["success"] = $success;
			$template = $twig->load('topics_insert_form.html.twig');
		}
	}
	if ($_GET['action'] == 'modifyDesc') {
		$template_info["header"] = 'Редактирование темы';
		$template_info["modify"] = true;
		if (preg_match("/^\d+$/", $_GET['record_id'])) {
			if (empty($_POST)) {
				$record_id = $_GET['record_id'];
				$template_info["success"] = false;
				$template_info["error"] = false;
				list($topics_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($record_id);
				$template_info["topic_id"] = $topics_id;
				$template_info["topic_text"] = $topic_desc;
				$template = $twig->load('topics_modify_form.html.twig');
			}
			else {
				$record_id = $_GET['record_id'];
				$topic_id = $_POST['topic_id'];		
				$topic_desc = $_POST['topic_text'];		
				$template_info["topic_id"] = $record_id;
				$template_info["topic_text"] = $topic_desc;
				$r_id = updateTopicDescByID($topic_id, $topic_desc);
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
					$template = $twig->load('blank.html.twig');
				}
			}
			$template = $twig->load('topics_modify_form.html.twig');
		}
		else {
			$error = 'В URL не указан record_id. Чтобы изменить запись найдите ее record_id (id)
			в Темы->Все темы и подставьте в URL.';
			$template_info["error"] = $error;
			$template_info["success"] = false;
			$template = $twig->load('blank.html.twig');
		}
	}		
	if ($_GET['action'] == 'insertDesc') {
		$template_info["header"] = 'Добавление темы';
		$template_info["modify"] = false;
        if (empty($_POST)) {
			$template_info["success"] = false;
			$template_info["error"] = false;
			$template = $twig->load('topics_modify_form.html.twig');
		}
		else {
			$_POST = array_map("trim",$_POST);
#			$_POST = array_map("addslashes",$_POST); 			
			$error = false;
			$success = false;
            $topic_id = $_POST['topic_id'];
            $topic_desc = $_POST['topic_text'];
			$r_id = updateTopicDescByID($topic_id,$topic_desc);
			if ($r_id > 0) {
				$success = 'Success! A new record was created id='.$r_id;
			}
			else {
				$error = 'An error occur! See DB log files.';
			}
			$template_info["error"] = $error;
			$template_info["success"] = $success;
			$template = $twig->load('topics_modify_form.html.twig');
		}
	}		
}
else {
	$template_info["content"] ='Requested page does not exist. Contact site admin. ';
	$template = $twig->load('page.html.twig');
}
echo $template->display($template_info);

