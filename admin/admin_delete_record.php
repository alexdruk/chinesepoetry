<?php
require_once dirname(__DIR__).'/globals.php';
$template_info["ERROR"] = $ERROR;
$template_info["title"] ='Delete record';
$success = false;
$error =false;

if ($_GET['table'] == 'poems') {
    $template_info["type"] = 'poems';
    $template_info["maintables"] = true;
    if ((array_key_exists('posted', $_GET)) && (array_key_exists('recordID', $_POST))) {
        $record_id = $_POST['recordID'];
        $rd = deleteRecordFromPoems($record_id );
        if ($rd > 0) {
            $success = "Record ".$record_id." was succcesfully deleted";
            $error = false;
        }
        else {
            $success = false;
            $error = "An error occur.";
        }
    }
}
elseif ($_GET['table'] == 'originals') {
    $template_info["type"] = 'originals';
    $template_info["maintables"] = true;
    if ((array_key_exists('posted', $_GET)) && (array_key_exists('recordID', $_POST))) {
        $record_id = $_POST['recordID'];
        $rd = deleteRecordFromOriginals($record_id);
        if ($rd > 0) {
            $success = "Record " . $record_id . " was succcesfully deleted";
            $error = false;
        } else {
            $success = false;
            $error = "An error occur.";
        }
    }
} elseif ($_GET['table'] == 'otherbiblio') {
    $template_info["type"] = 'otherbiblio';
    $template_info["maintables"] = false;
    if ((array_key_exists('posted', $_GET)) && (array_key_exists('poem_id', $_POST)) && (array_key_exists('main_biblio_id', $_POST))) {
        $poem_id = $_POST['poem_id'];
        $main_biblio_id = $_POST['main_biblio_id'];
        $rd = deleteRecordFromOtherbiblio($poem_id, $main_biblio_id);
        if ($rd > 0) {
            $success = "Record " . $rd . " was succcesfully deleted";
            $error = false;
        }
        else {
            $success = false;
            $error = "An error occur.";
        }
    }
}
$template_info["error"] = $error;
$template_info["success"] = $success;
$template = $twig->load('admin_delete_record.html.twig');
echo $template->display($template_info);