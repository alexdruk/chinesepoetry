<?php
/**
 * get a list of all records from table topics
 *
 * @return array arrayfo records
 * @throws DBException
 */
function getAllFromTopics() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT t.topics_id,t.topic_name,t.topic_synonym,t.present FROM  topics t ORDER BY t.topic_name ASC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($topics_id,$topic_name,$synonym,$present)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($topics_id,$topic_name,$synonym,$present);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get record by biblio_id from table biblio
 *
 * @return array array of values
 * @throws DBException
 */
function getByIDFromSources($biblioID) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT b.biblio_id,b.author,b.book_name,b.translator,b.ref_name,b.seria,b.publisher,b.year,b.code,b.biblio_name,b.ISBN,b.present FROM  biblio b  WHERE biblio_id=? ORDER BY b.biblio_id ASC;')) {
		if (!$stmt->bind_param('i', $biblioID)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table biblio
 *
 * @return array array of records
 * @throws DBException
 */
function getAllFromSources() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT b.biblio_id,b.author,b.book_name,b.translator,b.ref_name,b.seria,b.publisher,b.year,b.code,b.biblio_name,b.ISBN,b.present FROM  biblio b ORDER BY b.biblio_id ASC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * insert a new records into table biblio
 *
 * @param string  author Автор
 * @param string  book_name Название книги required
 * @param string  translator Составитель / переводчик  can be empty
 * @param string  ref_name Название ссылки в антологии required
 * @param string  seria Серия  can be empty
 * @param string  publisher Издательство can be empty
 * @param string  code Код книги can be empty
 * @param int year required
 * @param string  ISBN  can be empty
 * @param int in_antology if book is present in antology, can be 0, 1, 2, -1 required
 * @param string  biblio_name Полные выходные данные required
 * @return inserted record id
 * @throws DBException
 */
function sources_insert_record($author, $book_name, $translator, $ref_name, $seria, $publisher, $code, $year, $ISBN, $in_antology, $biblio_name) {
	$db = UserConfig::getDB();
	$author = (!empty($author)) ? $author : NULL;
	$translator = (!empty($translator)) ? $translator : NULL;
	$ref_name = (!empty($ref_name)) ? $ref_name : NULL;
	$seria = (!empty($seria)) ? $seria : NULL;
	$publisher = (!empty($publisher)) ? $publisher : NULL;
	$code = (!empty($code)) ? $code : NULL;
	$ISBN = (!empty($ISBN)) ? $ISBN : NULL;
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO `biblio` (`author`, `book_name`, `translator`, `ref_name`, `seria`, `publisher`, `code`, `year`, `ISBN`, `present`, `biblio_name`) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
		if (!$stmt->bind_param('sssssssisis', $author, $book_name, $translator, $ref_name, $seria, $publisher, $code, $year, $ISBN, $in_antology, $biblio_name)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * update a record into table biblio identifyed by record ID
 *
* @param biblio_id  record id
* @param string  author Автор
 * @param string  book_name Название книги required
 * @param string  translator Составитель / переводчик  can be empty
 * @param string  ref_name Название ссылки в антологии required
 * @param string  seria Серия  can be empty
 * @param string  publisher Издательство can be empty
 * @param string  code Код книги can be empty
 * @param int year required
 * @param string  ISBN  can be empty
 * @param int in_antology if book is present in antology, can be 0, 1, 2, -1 required
 * @param string  biblio_name Полные выходные данные required
 * @return inserted record id
 * @throws DBException
 */
function updateSourcesByID($biblio_id,$author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$biblio_name,$ISBN,$present) {
	$db = UserConfig::getDB();
	$author = (!empty($author)) ? $author : NULL;
	$translator = (!empty($translator)) ? $translator : NULL;
	$ref_name = (!empty($ref_name)) ? $ref_name : NULL;
	$seria = (!empty($seria)) ? $seria : NULL;
	$publisher = (!empty($publisher)) ? $publisher : NULL;
	$code = (!empty($code)) ? $code : NULL;
	$ISBN = (!empty($ISBN)) ? $ISBN : NULL;
#	$present = (!empty($present)) ? 0 : 1;
	$r_id = NULL;
/*	$sql = <<< SQL
	'UPDATE `biblio` SET `author`=$author, `book_name`=$book_name, `translator`=$translator,
	 `ref_name`=$ref_name, `seria`=$seria, `publisher`=$publisher, `code`=$code, `year`=$year, `ISBN`=$ISBN, 
	 `present`=$present, `biblio_name`=$biblio_name WHERE `biblio_id`= $biblio_id'
	SQL;
	echo $sql; 
*/	if ($stmt = $db->prepare('UPDATE `biblio` SET `author`=?, `book_name`=?, `translator`=?, `ref_name`=?, `seria`=?, `publisher`=?, `code`=?, `year`=?, `ISBN`=?, `present`=?, `biblio_name`=? WHERE `biblio_id`=?')) {
		if (!$stmt->bind_param('sssssssisisi', $author, $book_name, $translator, $ref_name, $seria, $publisher, $code, $year, $ISBN, $present, $biblio_name, $biblio_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * insert a new records into table topics
 *
 * @param string  topic_name Название темы required
 * @param string  synonym Синоним названия can be empty
 * @param int 	  presentAntology 0 or 1 required
 * @return inserted record id
 * @throws DBException
 */
function topics_insert_record($topic_name, $synonym, $presentAntology) {
	$db = UserConfig::getDB();
	$synonym = (!empty($synonym)) ? $synonym : NULL;
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO topics (topic_name, topic_synonym, presentAntology) 
		VALUES (?, ?, ?)')) {
		if (!$stmt->bind_param('ssi', $topic_name, $synonym, $presentAntology)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}

/**
 * get a list of all full biblio names from table biblio
 *
 * @param int  present 0 - нет, 1 - есть, 2 - будет,-1 - нет книги
 * @return array array of records
 * @throws DBException
 */
function getFullBiblio($present) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($present == 4) {
		$sql ='SELECT `biblio_name` FROM biblio ORDER BY `biblio_name` ASC';
	}
	elseif ($present == 1) {
		$sql ='SELECT `biblio_name` FROM biblio WHERE `present`=1  ORDER BY `biblio_name` ASC';
	}
	elseif ($present == -1) {
		$sql ='SELECT `biblio_name` FROM biblio WHERE `present`=-1  ORDER BY `biblio_name` ASC';
	}
	if ($stmt = $db->prepare($sql)) {
/*		if (!$stmt->bind_param('i', $present)) {
			throw new DBBindParamException($db, $stmt);
		}
*/		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($biblio_name)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = $biblio_name;
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * search table biblio for a pattern
 *
 * @param string pattern
 * @return array array of records
 * @throws DBException
 */
function searchBiblio($pattern) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	$pattern = mysqli_real_escape_string($db,$pattern);
	$val = str_replace("%", "", $pattern);
	$val = str_replace("_", "", $val);
	$like = '%'.$val.'%';
	$sql = "SELECT biblio_name FROM biblio WHERE MATCH (author,book_name,translator,ref_name,seria,publisher,code,biblio_name) against ('$pattern' IN NATURAL LANGUAGE MODE)
	UNION DISTINCT 
	SELECT biblio_name FROM biblio WHERE biblio_name LIKE '$like'";
	if ($stmt = $db->prepare($sql)) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($biblio_name)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = $biblio_name;
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $records;
}
/**
 * get record by biblio_id from table biblio
 *
 * @return array array of values
 * @throws DBException
 */
function getBiblioByID($biblioID) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT b.ref_name FROM  biblio b  WHERE biblio_id=?')) {
		if (!$stmt->bind_param('i', $biblioID)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($biblio_name)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($biblio_name);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get record by biblio_id from table biblio
 *
 * @return array array of values
 * @throws DBException
 */
function getFullBiblioByID($biblioID) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT b.biblio_name FROM  biblio b  WHERE biblio_id=?')) {
		if (!$stmt->bind_param('i', $biblioID)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($biblio_name)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($biblio_name);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}

/**
 * get a list of all records from table authors
 *
 * @return array array of records
 * @throws DBException
 */
function getAllfromAuthors() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT a.author_id, a.full_name, a.proper_name, a.dates,  a.epoch, a.present, b.zh_trad, b.zh_simple 
	FROM  authors a
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
	ORDER BY a.full_name ASC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($author_id, $full_name, $proper_name, $dates,  $epoch, $present, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($author_id, $full_name, $proper_name,  $dates, $epoch, $present, $zh_trad, $zh_simple);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * insert a new records into table authors
 *
 * @param string  full_name Полное имя с датами required
 * @param string  proper_name Правильное имя required
 * @param string  dates Годы жизни required
 * @param string  epoch Эпоха required
* @param int 	  Наличие в антологии 0 or 1 required
 * @return inserted record id
 * @throws DBException
 */
function authors_insert_record($full_name, $proper_name, $dates, $epoch, $present) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO authors (full_name, proper_name,  dates,  epoch, present) 
		VALUES (?, ?, ?, ?, ?)')) {
		if (!$stmt->bind_param('ssssi', $full_name, $proper_name,  $dates,  $epoch, $present)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * update a record into table biblio identifyed by record ID
 *
 * @param author_id  record id
 * @param string  full_name Полное имя с датами
 * @param string  proper_name Правильное имя
 * @param string  dates Годы жизни
 * @param string  epoch Эпоха
 * @param int present Наличие в антологии if author is present in antology, can be 0, 1
 * @return inserted record id
 * @throws DBException
 */
function updateAuthorsByID($author_id, $full_name, $proper_name,  $dates,  $epoch, $present) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE `authors` SET `full_name`=?, `proper_name`=?, `dates`=?,  `epoch`=?, `present`=?  WHERE `author_id`=?')) {
		if (!$stmt->bind_param('ssssii', $full_name, $proper_name,  $dates,  $epoch, $present, $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * get record by author_id from table authors
 *
 * @return array array of values
 * @throws DBException
 */
function getByIDFromAuthors($author_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT a.author_id, a.full_name, a.proper_name,  a.dates,  a.epoch, a.present, b.zh_trad, b.zh_simple
		FROM  authors a
		INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
		WHERE a.author_id=?;')) {
		if (!$stmt->bind_param('i', $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($author_id, $full_name, $proper_name,  $dates,  $epoch, $present, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($author_id, $full_name, $proper_name, $dates, $epoch, $present, $zh_trad, $zh_simple);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table authors by epoch name
 *
 * @return array array of records
 * @throws DBException
 */
function getAllfromAuthorsByEpoch($epoch) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT a.author_id, a.full_name, a.proper_name,  a.dates,  a.epoch, a.present, b.zh_trad, b.zh_simple 
	FROM  authors a 
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
	WHERE a.epoch=? ORDER BY a.full_name ASC;')) {
		if (!$stmt->bind_param('s', $epoch)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($author_id, $full_name, $proper_name,  $dates,  $epoch, $present, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($author_id, $full_name, $proper_name, $dates,  $epoch, $present, $zh_trad, $zh_simple);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * search table authors for a pattern
 *
 * @param string pattern
 * @return array array of records
 * @throws DBException
 */
function searchAuthors($pattern) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	$pattern = mysqli_real_escape_string($db,$pattern);
	$val = str_replace("%", "", $pattern);
	$val = str_replace("_", "", $val);
	$like = '%'.$val.'%';
	$sql = "SELECT a.author_id, a.full_name, a.proper_name, a.dates, a.epoch, a.present, b.zh_trad, b.zh_simple FROM authors a 
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id
	WHERE MATCH (a.full_name, a.proper_name, a.dates, a.epoch) against ('$pattern' IN NATURAL LANGUAGE MODE)
	UNION DISTINCT 
	SELECT a.author_id, a.full_name, a.proper_name, a.dates, a.epoch, a.present, b.zh_trad, b.zh_simple  FROM authors a 
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id
	WHERE a.full_name LIKE '$like' OR a.proper_name LIKE '$like' OR  a.dates LIKE '$like'
	UNION DISTINCT 
	SELECT a.author_id, a.full_name, a.proper_name, a.dates, a.epoch, a.present, b.zh_trad, b.zh_simple  FROM authors a 
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id
	WHERE a.author_id IN
	(SELECT author_id FROM authors_atrib  WHERE MATCH (forsearch) against ('$pattern'IN NATURAL LANGUAGE MODE));";

//print_r ($sql);
	if ($stmt = $db->prepare($sql)) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($author_id, $full_name, $proper_name, $dates, $epoch, $present, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($author_id, $full_name, $proper_name, $dates, $epoch, $present, $zh_trad, $zh_simple);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $records;
}
/**
 * get record by author_id from table authors 
 *
 * @return array array of values
 * @throws DBException
 */
function getDocByIDFromAuthors($record_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT a.proper_name, a.dates, a.epoch, d.doc_text FROM authors a 
	INNER JOIN a_description d ON  d.author_id = a.author_id
	WHERE a.author_id = ?;')) {
		if (!$stmt->bind_param('i', $record_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($proper_name, $dates, $epoch, $doc_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($proper_name, $dates, $epoch, $doc_text);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * modify a record in table a_description
 *
 * @param string  desc long html code
 * @param int 	  author id
 * @return inserted record id
 * @throws DBException
 */
function authors_modify_description($author_id, $desc) {
	$db = UserConfig::getDB();
#	$desc = mysqli_real_escape_string($db, $desc);
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE a_description  SET doc_text=? WHERE author_id=?')) {
		if (!$stmt->bind_param('si', $desc, $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}

/**
 * insert  a new record into table a_description
 *
 * @param string  desc long html code
 * @param int 	  author id
 * @return inserted record id
 * @throws DBException
 */
function authors_insert_description($author_id, $desc) {
	$db = UserConfig::getDB();
#	$desc = mysqli_real_escape_string($db, $desc);
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO a_description (`doc_text`,`author_id`) VALUES (?, ?)')) {
		if (!$stmt->bind_param('si', $desc, $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * insert  a new record into table a_description
 *
 * @param string  desc long html code
 * @param int 	  translator_id id
 * @return inserted record id
 * @throws DBException
 */
function translators_insert_description($translator_id, $full_name, $dates, $img, $summary, $doc_text) {
	$db = UserConfig::getDB();
#	$desc = mysqli_real_escape_string($db, $desc);
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO translators_desc (`translator_id`, `full_name`, `dates`, `img`, `summary`, `doc_text`)
	 VALUES (?, ?, ?, ?, ?, ?)')) {
		if (!$stmt->bind_param('isssss', $translator_id, $full_name, $dates, $img, $summary, $doc_text)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * modify a record in table translators_desc
 *
 * @param string  desc long html code
 * @param int 	  author id
 * @return inserted record id
 * @throws DBException
 */
function translators_modify_description($translator_id, $full_name, $dates, $summary, $img, $doc_text) {
	$db = UserConfig::getDB();
#	$desc = mysqli_real_escape_string($db, $desc);
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE translators_desc  SET `full_name`=?,
	 `dates`=?, `summary`=?, `img`=?, `doc_text`=? WHERE `translator_id`=?')) {
		if (!$stmt->bind_param('sssssi',$full_name, $dates, $summary, $img, $doc_text,  $translator_id )) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}


/**
 * get a list of all records from table translators
 *
 * @return array array of records
 * @throws DBException
 */
function getAllfromTranslators() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `translator_id`, `full_name`, `lit_name`, `real_name`, `first_name`, `father_name`, `pseudonyms`, `born`, `born_place`, `died`, `died_place`, `present` FROM  translators ORDER BY full_name ASC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table translators who is present in antology
 *
 * @return array array of records
 * @throws DBException
 */
function getAllfromPresentTranslators() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `translator_id`, `full_name`, `lit_name`, `real_name`, `first_name`, `father_name`, `pseudonyms`, `born`, `born_place`, `died`, `died_place`, `present` FROM  translators WHERE `present`=1 ORDER BY full_name ASC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * insert a new records into table translators
 *
 * @param string  full_name Полное имя с датами required
 * @param string  lit_name Литературная фамилия
 * @param string  real_name Настоящая (или девичья) фамилия
 * @param string  first_name Имя
 * @param string  father_name Отчество
 * @param string  pseudonyms Псевдонимы
 * @param string  born Дата рождения
 * @param string  born_place Место рождения
 * @param string  died Дата смерти
 * @param string  died_place Место смерти
* @param int 	  Наличие в антологии 0 or 1, 2 required
 * @return inserted record id
 * @throws DBException
 */
function translators_insert_record($full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO translators (`full_name`, `lit_name`, `real_name`, `first_name`, `father_name`, `pseudonyms`, `born`, `born_place`, `died`, `died_place`, `present`) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')) {
		if (!$stmt->bind_param('ssssssssssi', $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * update a record into table biblio identifyed by record ID
 *
 * @param author_id  record id
 * @param string  full_name Полное имя с датами
 * @param string  lit_name Литературная фамилия
 * @param string  real_name Настоящая (или девичья) фамилия
 * @param string  first_name Имя
 * @param string  father_name Отчество
 * @param string  pseudonyms Псевдонимы
 * @param string  born Дата рождения
 * @param string  born_place Место рождения
 * @param string  died Дата смерти
 * @param string  died_place Место смерти
 * @param int present Наличие в антологии if author is present in antology, can be 0, 1
 * @return inserted record id
 * @throws DBException
 */
function updateTranslatorsByID($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present) {
	$db = UserConfig::getDB();
	$lit_name = (!empty($lit_name)) ? $lit_name : NULL;
	$real_name = (!empty($real_name)) ? $real_name : NULL;
	$first_name = (!empty($first_name)) ? $first_name : NULL;
	$father_name = (!empty($father_name)) ? $father_name : NULL;
	$pseudonyms = (!empty($pseudonyms)) ? $pseudonyms : NULL;
	$born = (!empty($born)) ? $born : NULL;
	$born_place = (!empty($born_place)) ? $born_place : NULL;
	$died = (!empty($died)) ? $died : NULL;
	$died_place = (!empty($died_place)) ? $died_place : NULL;
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE `translators` SET `full_name`=?, `lit_name`=?, `real_name`=?, `first_name`=?, `father_name`=?, `pseudonyms`=?, `born`=?, `born_place`=?, `died`=?, `died_place`=?, `present`=?  WHERE `translator_id`=?')) {
		if (!$stmt->bind_param('ssssssssssii', $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present, $translator_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * get record by translator_id from table translators
 *
 * @return array array of values
 * @throws DBException
 */
function getByIDFromTranslators($translator_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT `translator_id`,`full_name`, `lit_name`, `real_name`, `first_name`, `father_name`, `pseudonyms`, `born`, `born_place`, `died`, `died_place`, `present` FROM  translators  WHERE translator_id=?;')) {
		if (!$stmt->bind_param('i', $translator_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($translator_id, $full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $born, $born_place, $died, $died_place, $present);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * search table translators for a pattern
 *
 * @param string pattern
 * @return array array of records
 * @throws DBException
 */
function searchTranslators($pattern) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	$pattern = mysqli_real_escape_string($db,$pattern);
	$val = str_replace("%", "", $pattern);
	$val = str_replace("_", "", $val);
	$like = '%'.$val.'%';
	$sql = "SELECT translator_id, full_name FROM translators a WHERE MATCH (`full_name`, `lit_name`, `real_name`, `first_name`, `father_name`, `pseudonyms`, `born`, `born_place`, `died`, `died_place`) against ('$pattern' IN NATURAL LANGUAGE MODE)
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE full_name LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE lit_name LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE real_name LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE first_name LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE father_name LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE pseudonyms LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE born LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE born_place LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE died LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators  WHERE died_place LIKE '$like'
	UNION DISTINCT 
	SELECT translator_id, full_name FROM translators_desc a WHERE MATCH (`full_name`,`dates`,`summary`,`doc_text`) against ('$pattern' IN NATURAL LANGUAGE MODE)";
	if ($stmt = $db->prepare($sql)) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($translator_id, $full_name)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($translator_id, $full_name);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $records;
}
/**
 * get a list of all records from table originals
 *
 * @return array array of records
 * @throws DBException
 * originals_id INT  PRIMARY KEY,
 */
function getListfromOriginals() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT o.originals_id, a.author_id, a.proper_name, a.dates, o.cycle_zh, o.cycle_ru, o.subcycle_zh, o.subcycle_ru, 
	o.poem_code, o.biblio_id, o.poem_name_zh, o.poem_name_ru, a.epoch, b.zh_trad, b.zh_simple
	FROM originals o
	INNER JOIN authors a ON a.author_id = o.author_id 
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
	ORDER BY a.proper_name, o.originals_id ASC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id, $author_id, $proper_name, $dates, $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_code, $biblio_id, $poem_name_zh, $poem_name_ru, $epoch, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id, $author_id, $proper_name, $dates, $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_code, $biblio_id, $poem_name_zh, $poem_name_ru, $epoch, $zh_trad, $zh_simple);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table originals
 *
 * @return array array of records
 * @throws DBException
 * originals_id INT  PRIMARY KEY,
 */
function getRecordfromOriginals($originals_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT o.originals_id, a.author_id, a.proper_name, a.dates, o.cycle_zh, o.cycle_ru, o.subcycle_zh, o.subcycle_ru, 
	o.poem_code, o.biblio_id, o.poem_name_zh, o.poem_name_ru, a.epoch, b.zh_trad, b.zh_simple FROM originals o
	INNER JOIN authors a ON a.author_id = o.author_id
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
	 WHERE o.originals_id =? 
	 ORDER BY a.proper_name, o.originals_id ASC;')) {
		if (!$stmt->bind_param('i', $originals_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id, $author_id, $proper_name, $dates, $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_code, $biblio_id, $poem_name_zh, $poem_name_ru, $epoch, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id, $author_id, $proper_name, $dates, $cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru, $poem_code, $biblio_id, $poem_name_zh, $poem_name_ru, $epoch, $zh_trad, $zh_simple);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get record by author-id from table originals
 *
 * @param author_id  record id
 * @return array array of values
 * @throws DBException
 */
function getOriginalsByAuthorID($author_id) {
	$db = UserConfig::getDB();
	$record = null;
	$records = array();
	if ($stmt = $db->prepare('SELECT o.originals_id, o.author_id, o.cycle_zh, o.cycle_ru, o.subcycle_zh, o.subcycle_ru, 
	o.poem_name_zh, o.poem_name_ru, o.poem_code,o.biblio_id  FROM originals o
	 WHERE o.author_id=? ORDER BY o.corder, o.cycle_ru, o.scorder, o.subcycle_ru,  cast(o.poem_name_ru as UNSIGNED INTEGER),o.poem_name_ru,  o.originals_id ASC;')) {
		if (!$stmt->bind_param('i', $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id,$author_id,$cycle_zh, $cycle_ru, $subcycle_zh, $subcycle_ru,$poem_name_zh, $poem_name_ru,$poem_code,$biblio_id)) {

			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id,$author_id,'','','','','','','',$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get record by originals_id from table originals
 *
 * @param originals_id  record id
 * @return array array of values
 * @throws DBException
 */
function getOriginalsByPoemID($originals_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT o.originals_id, a.author_id, a.proper_name, a.dates, a.epoch, o.cycle_zh, o.cycle_ru, o.corder, o.subcycle_zh, o.subcycle_ru, o.scorder,
	 o.poem_name_zh, o.poem_name_ru, o.poem_code,o.biblio_id,o.poem_text,o.genres,o.size, b.zh_trad, b.zh_simple, o.site, o.siteURL 
	FROM originals o
	INNER JOIN authors a ON a.author_id = o.author_id 
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
	WHERE o.originals_id=? ORDER BY o.corder, o.poem_name_ru;')) {
		if (!$stmt->bind_param('i', $originals_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id,$author_id,$proper_name, $dates,$epoch,$cycle_zh, $cycle_ru, $corder, $subcycle_zh, $subcycle_ru, $scorder, 
		$poem_name_zh, $poem_name_ru,$poem_code,$biblio_id,$poem_text,$genres,$size, $zh_trad, $zh_simple, $site, $siteURL )) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id,$author_id,$proper_name, $dates,$epoch,$cycle_zh, $cycle_ru, $corder, $subcycle_zh, $subcycle_ru, $scorder, 
			$poem_name_zh, $poem_name_ru,$poem_code,$biblio_id,$poem_text,$genres,$size, $zh_trad, $zh_simple, $site, $siteURL);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}

/**
 * insert a new records into table originals
 *
 * @param int 	  author_id * @param string  cycle
 * @param string  subcycle
 * @param string  poem_name required
 * @param string  epoem_text required
 * @return inserted record id
 * @throws DBException
 */
function originals_insert_record($author_id, $cycle_zh, $cycle_ru, $corder, 
$subcycle_zh, $subcycle_ru, $scorder, $poem_name_zh, $poem_name_ru, $poem_code, 
$biblio_id, $poem_text, $genres, $size, $site, $siteURL) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO originals (author_id, cycle_zh, cycle_ru, 
	corder, subcycle_zh, subcycle_ru, scorder, poem_name_zh, poem_name_ru, poem_code, 
	biblio_id, poem_text, genres, size, `site`, siteURL) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)')) {
		if (!$stmt->bind_param('issississsisssss', $author_id, $cycle_zh, $cycle_ru, $corder,
		$subcycle_zh, $subcycle_ru, $scorder, $poem_name_zh, $poem_name_ru, 
		$poem_code, $biblio_id, $poem_text, $genres, $size, $site, $siteURL)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}

/**
 * update a record into table original identifyed by record ID
 *
 * @param originals_id  record id
 * @param string  cycle
 * @param string  subcycle
 * @param string  biblio_id
 * @param string  poem_code
 * @param string  poem_name
 * @param string  poem_text
 * @return inserted record id
 * @throws DBException
 */
function updateOriginalPoemByID($originals_id, $cycle_zh, $cycle_ru, $corder,
$subcycle_zh, $subcycle_ru, $scorder, $biblio_id, $poem_code, $poem_name_zh,  
$poem_name_ru, $poem_text, $genres, $size, $site, $siteURL) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE `originals` SET `cycle_zh`=?, `cycle_ru`=?, `corder`=?, `subcycle_zh`=?,  `subcycle_ru`=?, `scorder`=?, 
	`biblio_id`=?, `poem_code`=?, `poem_name_zh`=?, `poem_name_ru`=?, `poem_text`=?, `genres`=?, `size`=?, `site`=?, `siteURL`=?  WHERE `originals_id`=?')) {
		if (!$stmt->bind_param('ssissiissssssssi', $cycle_zh, $cycle_ru, $corder, $subcycle_zh, $subcycle_ru, $scorder, $biblio_id, $poem_code, 
		$poem_name_zh,  $poem_name_ru, $poem_text, $genres, $size, $site, $siteURL, $originals_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * get a list of all records from table poems without text
 *
 * @return array array of records
 * @throws DBException
 */
function getWithoutPoem_textFromPoemsByAuthorID($author_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`, `subcycle_zh`,`subcycle_ru`,
	`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id` FROM  poems WHERE author_id = ? 
	ORDER BY `corder`,`cycle_ru`, `scorder`, `subcycle_ru`, `author_id`, `translator1_id`, cast(`poem_name_ru` as UNSIGNED  INTEGER),
	`poem_name_ru`, `poems_id` ASC;')) {
		if (!$stmt->bind_param('i', $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table poems with text
 *
 * @return array array of records
 * @throws DBException
 */
function getAllFromPoemsByAuthorID($author_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash`
	FROM  poems WHERE author_id = ? ORDER BY poems_id ASC;')) {
		if (!$stmt->bind_param('i', $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table poems without text
 *
 * @return array array of records
 * @throws DBException
 */
function getWithoutPoem_textFromPoemsByTranslatorID($translator_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,`subcycle_ru`,
	`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id` FROM  poems WHERE 
	translator1_id = ? OR translator2_id = ? 
	ORDER BY `scorder`,`subcycle_ru`, `author_id`, cast(`poem_name_ru` as UNSIGNED INTEGER),`poem_name_ru`, `corder`,`cycle_ru`, `poems_id` ASC;')) {
		if (!$stmt->bind_param('ii', $translator_id,$translator_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a record from table poems with poem_id
 *
 * @return array array of records
 * @throws DBException
 */
function getPoemsByPoemID($poem_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash`,`site`,`siteURL` 
	 FROM  poems WHERE poems_id = ?;')) {
		if (!$stmt->bind_param('i', $poem_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poem_hash, $site, $siteURL)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poem_hash,$site, $siteURL);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a record from table poems with poem_id
 *
 * @return array array of records
 * @throws DBException
 */
function getPoemsByCycleTranslator($cycle, $translator_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT p.poems_id, a.author_id, a.proper_name, a.dates, a.epoch, p.translator1_id, p.translator2_id,
	 p.cycle_zh, p.cycle_ru, p.subcycle_zh, p.subcycle_ru, p.poem_name_zh, p.poem_name_ru, p.poem_text 
	 FROM  poems p 
	 INNER JOIN authors a ON a.author_id = p.author_id 
	 WHERE p.cycle_ru = ?  and (p.translator1_id = ? or p.translator2_id = ?) 
	 ORDER BY  p.scorder, p.subcycle_ru, cast(p.poem_name_ru as UNSIGNED INTEGER),p.poem_name_ru, p.corder, p.cycle_ru, p.poems_id ASC;')) {
		if (!$stmt->bind_param('sii', $cycle, $translator_id, $translator_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id, $proper_name, $dates, $epoch, $translator1_id,$translator2_id,
		$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id, $proper_name, $dates, $epoch, $translator1_id,$translator2_id,
			$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a record from table poems with poem_id
 *
 * @return array array of records
 * @throws DBException
 */
function getPoemsBySubCycleTranslator($subcycle, $translator_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT p.poems_id, a.author_id, a.proper_name, a.dates, a.epoch, p.translator1_id, p.translator2_id,
	 p.cycle_zh, p.cycle_ru, p.subcycle_zh, p.subcycle_ru, p.poem_name_zh, p.poem_name_ru, p.poem_text 
	 FROM  poems p 
	 INNER JOIN authors a ON a.author_id = p.author_id 
	 WHERE p.subcycle_ru = ?  and (p.translator1_id = ? or p.translator2_id = ?) 
	 ORDER BY  cast(p.poem_name_ru as UNSIGNED INTEGER),p.poem_name_ru, p.corder, p.cycle_ru,  p.poems_id ASC;')) {
		if (!$stmt->bind_param('sii', $subcycle, $translator_id, $translator_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id, $proper_name, $dates, $epoch, $translator1_id,$translator2_id,
		$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id, $proper_name, $dates, $epoch, $translator1_id,$translator2_id,
			$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a record from table poems with poem_id
 *
 * @return array array of records
 * @throws DBException
 */
function getOriginalsByCycleZH($cycle_zh) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT p.originals_id, a.author_id, a.proper_name, a.dates, a.epoch,
	 p.cycle_zh, p.cycle_ru, p.subcycle_zh, p.subcycle_ru,  p.poem_name_zh, p.poem_name_ru, p.poem_text 
	 FROM  originals p 
	 INNER JOIN authors a ON a.author_id = p.author_id 
	 WHERE p.cycle_zh = ? 
	 ORDER BY  p.scorder, p.subcycle_zh, cast(p.poem_name_ru as UNSIGNED INTEGER),p.poem_name_ru, p.corder, p.cycle_ru,  p.originals_id ASC;')) {
		if (!$stmt->bind_param('s', $cycle_zh)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id,$author_id, $proper_name, $dates, $epoch,
		$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id,$author_id, $proper_name, $dates, $epoch,
			$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a record 
 *
 * @return array array of records
 * @throws DBException
 */
function getOriginalsBySubCycleZH($subcycle_zh) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT p.originals_id, a.author_id, a.proper_name, a.dates, a.epoch,
	 p.cycle_zh, p.cycle_ru, p.subcycle_zh, p.subcycle_ru, p.poem_name_zh, p.poem_name_ru, p.poem_text 
	 FROM  originals p 
	 INNER JOIN authors a ON a.author_id = p.author_id 
	 WHERE p.subcycle_zh = ? 
	 ORDER BY  p.corder, cast(p.poem_name_ru as UNSIGNED INTEGER),p.poem_name_ru,p.cycle_ru,  p.originals_id ASC;')) {
		if (!$stmt->bind_param('s', $subcycle_zh)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id,$author_id, $proper_name, $dates, $epoch,
		$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id,$author_id, $proper_name, $dates, $epoch,
			$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,$poem_name_zh,$poem_name_ru,$poem_text);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a random  poem_id
 *
 * @return array random poem_id
 * @throws DBException
 */
function getRandomPoemID() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id` FROM  poems ORDER BY RAND() LIMIT 1;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = $poems_id;
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table authors by topic
 *
 * @return array array of records
 * @throws DBException
 */
function getAllfromAuthorsByTopic($topic_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT distinct a.author_id, a.full_name, a.proper_name, a.dates, a.epoch, b.zh_trad, b.zh_simple 
	FROM  authors a 
	INNER JOIN poems p ON  a.author_id = p.author_id
	INNER JOIN  authors_atrib b ON b.author_id = a.author_id 
	WHERE p.topic1_id =? OR p.topic2_id =? OR p.topic3_id =?  OR p.topic4_id =?  OR p.topic5_id =? 
	ORDER BY a.full_name ASC;')) {
		if (!$stmt->bind_param('iiiii', $topic_id,$topic_id,$topic_id,$topic_id,$topic_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($author_id, $full_name, $proper_name, $dates, $epoch, $zh_trad, $zh_simple)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($author_id, $full_name, $proper_name, $dates, $epoch, $zh_trad, $zh_simple);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table poems without text
 *
 * @return array array of records
 * @throws DBException
 */
function getWithoutPoem_textFromPoemsByTopicID($topic_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`, `subcycle_zh`,`subcycle_ru`,
	`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id` FROM  poems 
	 WHERE topic1_id =? OR topic2_id =? OR topic3_id =?  OR topic4_id =?  OR topic5_id =? 
	 ORDER BY `corder`, `cycle_ru`, `scorder`, `subcycle_ru`,`author_id`, `translator1_id`, cast(`poem_name_ru` as UNSIGNED INTEGER),`poem_name_ru`,`cycle_ru`, `subcycle_ru`, `poems_id` ASC;')) {
		if (!$stmt->bind_param('iiiii', $topic_id,$topic_id,$topic_id,$topic_id,$topic_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get record by topic id from table topics
 *
 * @return array array of values
 * @throws DBException
 */
function getTopicByID($topics_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT topics_id,topic_name, topic_synonym, topic_text FROM topics WHERE topics_id =?;')) {
		if (!$stmt->bind_param('i', $topics_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($topics_id,$topic_name,$topic_synonym, $topic_desc )) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($topics_id,$topic_name,$topic_synonym,$topic_desc);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * search table topics for a pattern
 *
 * @param string pattern
 * @return array array of records
 * @throws DBException
 */
function searchTopics($pattern) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	$pattern = mysqli_real_escape_string($db,$pattern);
	$val = str_replace("%", "", $pattern);
	$val = str_replace("_", "", $val);
	$like = '%'.$val.'%';
	$sql = "SELECT topics_id,topic_name FROM topics WHERE MATCH (topic_name,topic_text) against ('$pattern' IN NATURAL LANGUAGE MODE)
	UNION DISTINCT 
	SELECT topics_id,topic_name FROM topics WHERE topic_name LIKE '$like'
	UNION DISTINCT 
	SELECT topics_id,topic_name FROM topics WHERE topic_text LIKE '$like'";
	if ($stmt = $db->prepare($sql)) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($topics_id,$topic_name)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($topics_id,$topic_name);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $records;
}
/**
 * update from table topics
 *	topic_id - record id
 *  topic_desc - topic text
 * @return id of modifyed record
 * @throws DBException
 */
function updateTopicDescByID($topic_id, $topic_desc) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('UPDATE topics SET topic_text=? WHERE topics_id =?;')) {
		if (!$stmt->bind_param('si', $topic_desc,$topic_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}

/**
 * insert a new records into table poems
 * @return inserted record id
 * @throws DBException
 */
function poems_insert_record($author_id,$translator1_id,$translator2_id,
	$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
	$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poem_hash,$totallines,$fulllines,$genres,$site,$siteURL) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO poems (author_id,translator1_id,translator2_id,
	topic1_id,topic2_id,topic3_id,topic4_id,topic5_id,cycle_zh,cycle_ru,corder,subcycle_zh,subcycle_ru,scorder,
	poem_name_zh,poem_name_ru,poem_code,biblio_id,poem_text,poem_hash,totallines,fulllines,genres,site,siteURL) 
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')) {
		if (!$stmt->bind_param('iiiiiiiissississsissiisss', $author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poem_hash,$totallines,$fulllines,$genres,$site,$siteURL)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * get record by poems_id from table poems
 *
 * @return array array of values
 * @throws DBException
 */
function getByIDFromPoems($poems_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT author_id,translator1_id,translator2_id,
	topic1_id,topic2_id,topic3_id,topic4_id,topic5_id,cycle_zh,cycle_ru,corder,subcycle_zh,subcycle_ru,scorder,
	poem_name_zh,poem_name_ru,poem_code,biblio_id,poem_text,totallines,fulllines,genres,`site`,`siteURL` FROM  poems WHERE poems_id=?;')) {
		if (!$stmt->bind_param('i', $poems_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$totallines,$fulllines,$genres,$site,$siteURL)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$totallines,$fulllines,$genres,$site,$siteURL);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * update a record into table poems identifyed by record ID
 *
 * @return inserted record id
 * @throws DBException
 */
function updatePoemsByID($poems_id,$author_id,$translator1_id,$translator2_id,
$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$totallines,$fulllines,$genres,$site,$siteURL) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE `poems` SET 
	author_id=?,translator1_id=?,translator2_id=?,
	topic1_id=?,topic2_id=?,topic3_id=?,topic4_id=?,topic5_id=?,cycle_zh=?,cycle_ru=?,corder=?,subcycle_zh=?,subcycle_ru=?,scorder=?,
	poem_name_zh=?,poem_name_ru=?,poem_code=?,biblio_id=?,poem_text=?,totallines=?,fulllines=?,genres=?,`site`=?, `siteURL`=?
	  WHERE `poems_id`=?')) {
		if (!$stmt->bind_param('iiiiiiiissississsisiisssi', $author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$corder,$subcycle_zh,$subcycle_ru,$scorder,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$totallines,$fulllines,$genres,$site,$siteURL,$poems_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * search table poems for a pattern
 *
 * @param string pattern
 * @return array array of records
 * @throws DBException
 */
function searchPoems($pattern) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	$pattern = mysqli_real_escape_string($db,$pattern);
	$val = str_replace("%", "", $pattern);
	$val = str_replace("_", "", $val);
	$like = '%'.$val.'%';
	$sql = "SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash`
	 FROM poems a WHERE MATCH (cycle_zh,cycle_ru,subcycle_zh,subcycle_ru,poem_name_zh, poem_name_ru,poem_text) against ('$pattern' IN NATURAL LANGUAGE MODE)
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash` 
	FROM poems  WHERE cycle_zh LIKE '$like'
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash` 
	FROM poems WHERE cycle_ru LIKE '$like'
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash` 
	FROM poems WHERE subcycle_zh LIKE '$like'
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash` 
	FROM poems WHERE subcycle_ru LIKE '$like'
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash` 
	FROM poems WHERE poem_name_zh LIKE '$like'
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash`
	FROM poems WHERE poem_name_ru LIKE '$like'
	UNION DISTINCT 
	SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`,`subcycle_zh`,
	`subcycle_ru`,`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id`,`poem_text`,`poem_hash` 
	FROM poems WHERE poem_text LIKE '$like'";
//print_r ($sql);
	if ($stmt = $db->prepare($sql)) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poems_hash)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id,$poem_text,$poems_hash);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $records;
}
/**
 * search table originals for a pattern
 *
 * @param string pattern
 * @return array array of records
 * @throws DBException
 */
function searchOriginals($pattern) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	$pattern = mysqli_real_escape_string($db,$pattern);
	$val = str_replace("%", "", $pattern);
	$val = str_replace("_", "", $val);
	$like = '%'.$val.'%';
	$sql = "SELECT originals_id FROM originals WHERE MATCH (cycle_zh,cycle_ru,subcycle_zh,subcycle_ru,poem_name_zh, poem_name_ru,poem_text) against ('$pattern' IN NATURAL LANGUAGE MODE)
	UNION DISTINCT 
	SELECT originals_id FROM originals  WHERE cycle_zh LIKE '$like'
	UNION DISTINCT 
	SELECT originals_id FROM originals WHERE cycle_ru LIKE '$like'
	UNION DISTINCT 
	SELECT originals_id FROM originals WHERE subcycle_zh LIKE '$like'
	UNION DISTINCT 
	SELECT originals_id FROM originals WHERE subcycle_ru LIKE '$like'
	UNION DISTINCT 
	SELECT originals_id FROM originals WHERE poem_name_zh LIKE '$like'
	UNION DISTINCT 
	SELECT originals_id FROM originals WHERE poem_name_ru LIKE '$like'
	UNION DISTINCT 
	SELECT originals_id FROM originals WHERE poem_text LIKE '$like'";
	if ($stmt = $db->prepare($sql)) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
#			$record = $originals_id;
			array_push($records, $originals_id);
		}
		$stmt->free_result();
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $records;
}

/**
 * get $originals_id by poem_code from table originals 
 *
 * @return array $originals_id
 * @throws DBException
 */
function getOriginalByPoemCode($poem_code) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT originals_id, poem_name_zh, poem_name_ru FROM originals WHERE poem_code = ?;')) {
		if (!$stmt->bind_param('s', $poem_code)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($originals_id, $poem_name_zh, $poem_name_ru)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($originals_id, $poem_name_zh, $poem_name_ru);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get other translation by poem_code from table poems 
 *
 * @return array $originals_id
 * @throws DBException
 */
function getOtherTranslationsByPoemCode($poem_code) {
	$db = UserConfig::getDB();
	$record = null;
	$records = array();
	if ($stmt = $db->prepare('SELECT p.poems_id, p.translator1_id, p.translator2_id, t.full_name, p.poem_name_zh, p.poem_name_ru 
		FROM poems p
		INNER JOIN  translators t ON t.translator_id = p.translator1_id
	    WHERE p.poem_code = ? ORDER BY t.full_name;')) {
		if (!$stmt->bind_param('s', $poem_code)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id, $translator1_id, $translator2_id, $full_name, $poem_name_zh, $poem_name_ru)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id, $translator1_id, $translator2_id, $full_name, $poem_name_zh, $poem_name_ru);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
function makePoemName ($poem_name_zh, $poem_name_ru) {
    if ($poem_name_zh) {
        $poem_name ='<span class="poem_name zh">'.$poem_name_zh.'</span> <span class="poem_name ru">'.$poem_name_ru.'</span>';
    }
    else {
        $poem_name ='<span class="poem_name ru">'.$poem_name_ru.'</span>';
    }
    return $poem_name;
}
/**
 * get record by author-id from table authors_atrib
 *
 * @param author_id  record id
 * @return array array of values
 * @throws DBException
 */
function getAtributesByAuthorID($author_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT `atrib_id`,`author_id`,`palladian`,`zh_trad`,`zh_simple`,`pinyin`,`real_name`,`real_name_zh`,`real_name_simple`,
	`real_name_pinyin`,`second_name`,`second_name_zh`,`second_name_simple`,`second_name_pinyin`,`postmortem_name`,
	`postmortem_name_zh`,`postmortem_name_simple`,`postmortem_name_pinyin`,`pseudonim_name`,`pseudonim_name_zh`,
	`pseudonim_name_simple`,`pseudonim_name_pinyin`,`nickname`,`nickname_zh`,`nickname_simple`,`nickname_pinyin`,`forsearch`
	 FROM authors_atrib WHERE author_id=? ;')) {
		if (!$stmt->bind_param('i', $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($id,$author_id,$palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,$real_name_simple,
		$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,$postmortem_name,
		$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,$pseudonim_name,$pseudonim_name_zh,
		$pseudonim_name_simple,$pseudonim_name_pinyin,$nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,
		$forsearch)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($id,$author_id,$palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,$real_name_simple,
			$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,$postmortem_name,
			$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,$pseudonim_name,$pseudonim_name_zh,
			$pseudonim_name_simple,$pseudonim_name_pinyin,$nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,
			$forsearch);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * insert  a new record into table authors_atrib
 *
 * @param int 	  author id
 * @return inserted record id
 * @throws DBException
 */
function authors_insert_atrib($author_id, $palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,
$real_name_simple,$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,
$postmortem_name,$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,
$pseudonim_name,$pseudonim_name_zh,$pseudonim_name_simple,$pseudonim_name_pinyin,
$nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,$forsearch) {
	$db = UserConfig::getDB();
#	$desc = mysqli_real_escape_string($db, $desc);
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO authors_atrib (`author_id`,`palladian`,`zh_trad`,`zh_simple`,`pinyin`,
	`real_name`,`real_name_zh`,`real_name_simple`,`real_name_pinyin`,`second_name`,`second_name_zh`,
	`second_name_simple`,`second_name_pinyin`,`postmortem_name`,`postmortem_name_zh`,`postmortem_name_simple`,
	`postmortem_name_pinyin`,`pseudonim_name`,`pseudonim_name_zh`,`pseudonim_name_simple`,`pseudonim_name_pinyin`,
	`nickname`,`nickname_zh`,`nickname_simple`,`nickname_pinyin`,`forsearch`)
	 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')) {
		if (!$stmt->bind_param('isssssssssssssssssssssssss', $author_id, $palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,
		$real_name_simple,$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,
		$postmortem_name,$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,
		$pseudonim_name,$pseudonim_name_zh,$pseudonim_name_simple,$pseudonim_name_pinyin,
		$nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,$forsearch)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * modify  a  record into table authors_atrib
 *
 * @param int 	  author id
 * @return inserted record id
 * @throws DBException
 */
function authors_modify_atrib($author_id, $palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,
$real_name_simple,$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,
$postmortem_name,$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,
$pseudonim_name,$pseudonim_name_zh,$pseudonim_name_simple,$pseudonim_name_pinyin,
$nickname,$nickname_zh,$nickname_simple,$nickname_pinyin,$forsearch) {
	$db = UserConfig::getDB();
#	$desc = mysqli_real_escape_string($db, $desc);
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE authors_atrib SET `palladian`=?, `zh_trad`=?, `zh_simple`=?, 
	`pinyin`=?, `real_name`=?, `real_name_zh`=?, `real_name_simple`=?, `real_name_pinyin`=?, 
	`second_name`=?, `second_name_zh`=?, `second_name_simple`=?, `second_name_pinyin`=?, 
	`postmortem_name`=?, `postmortem_name_zh`=?, `postmortem_name_simple`=?, 
	`postmortem_name_pinyin`=?, `pseudonim_name`=?, `pseudonim_name_zh`=?, `pseudonim_name_simple`=?, 
	`pseudonim_name_pinyin`=?, `nickname`=?, `nickname_zh`=?, `nickname_simple`=?, 
	`nickname_pinyin`=?, `forsearch`=? WHERE `author_id`=?')) {
		if (!$stmt->bind_param('sssssssssssssssssssssssssi',  $palladian,$zh_trad,$zh_simple,$pinyin,$real_name,$real_name_zh,
		$real_name_simple,$real_name_pinyin,$second_name,$second_name_zh,$second_name_simple,$second_name_pinyin,
		$postmortem_name,$postmortem_name_zh,$postmortem_name_simple,$postmortem_name_pinyin,
		$pseudonim_name,$pseudonim_name_zh,$pseudonim_name_simple,$pseudonim_name_pinyin,
		$nickname,$nickname_zh,$nickname_simple,$nickname_pinyin, $forsearch, $author_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * get record by translator_id from table translators_desc
 *
 * @return array array of values
 * @throws DBException
 */
function getTranslatorDescByID($translator_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT  `record_id`,`translator_id`,`full_name`,`dates`,`summary`,`img`,`doc_text`
	FROM  translators_desc WHERE translator_id=?;')) {
		if (!$stmt->bind_param('i', $translator_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($id, $translator_id,$full_name,$dates,$summary,$img,$doc_text)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($id, $translator_id,$full_name,$dates,$summary,$img,$doc_text);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get  translator_id from by traslator full name table translators
 *
 * @return translator_id
 * @throws DBException
 */
function getTranslatorIDByName($translator_fullname) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT  `translator_id`
	FROM  translators WHERE full_name=? LIMIT 1;')) {
		if (!$stmt->bind_param('s', $translator_fullname)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($translator_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = $translator_id;
		}
		$stmt->free_result();
		$stmt->close();
//		echo 'id='.$record;
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * insert a new records into table mistakes
 *
 * @param string  mistake
 * @param string  ip
 * @return inserted record id
 * @throws DBException
 */
function mistakes_insert_record($mistake, $ip, $url, $comment) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO mistakes (`mistake`, `ip`, `url`, `comments`) 
		VALUES (?, ?, ?, ?)')) {
		if (!$stmt->bind_param('ssss', $mistake, $ip, $url, $comment)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * check if record exists in table mistakes
 *
 * @param string  mistake
 * @param string  ip
 * @param string  url
 * @param string  url
 * @return inserted record id
 * @throws DBException
 */
function check_mistakes($mistake, $ip, $url) {
	$db = UserConfig::getDB();
	$record = null;
	$records = array();
	if ($stmt = $db->prepare('SELECT `mistake`, `ip`, `url` FROM mistakes WHERE 
		`mistake`=? AND `ip`=? AND `url`=?;')) {
		if (!$stmt->bind_param('sss', $mistake, $ip, $url)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($mistake, $ip, $url)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($mistake, $ip, $url);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all records from table poems without text
 * @param int biblio_id
 * @return array array of records
 * @throws DBException
 */
function getWithoutPoem_textFromPoemsByBiblioID($biblio_id) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `poems_id`,`author_id`,`translator1_id`,`translator2_id`,
	`topic1_id`,`topic2_id`,`topic3_id`,`topic4_id`,`topic5_id`,`cycle_zh`,`cycle_ru`, `subcycle_zh`,`subcycle_ru`,
	`poem_name_zh`,`poem_name_ru`,`poem_code`,`biblio_id` FROM  poems 
	 WHERE biblio_id =?
	 ORDER BY `author_id`, `translator1_id`, cast(`poem_name_ru` as UNSIGNED INTEGER),`poem_name_ru`,`corder`,`cycle_ru`, `scorder`,`subcycle_ru`, `poems_id` ASC;')) {
		if (!$stmt->bind_param('i', $biblio_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($poems_id,$author_id,$translator1_id,$translator2_id,
		$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
		$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($poems_id,$author_id,$translator1_id,$translator2_id,
			$topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
			$poem_name_zh,$poem_name_ru,$poem_code,$biblio_id);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all news from table news without fulltext
 *
 * @return array array of records
 * @throws DBException
 */
function getAllNewsWithoutFullText() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `news_id`,`header`,`home`,`ndate` FROM  news ORDER BY ndate DESC, news_id  DESC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($news_id, $header,$text,$ndate)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($news_id, $header,$text,$ndate);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of limited number news from table news without fulltext
 *
 *
 * @return array array of records
 * @throws DBException
 */
function getLimitedNewsWithoutFullText() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `news_id`,`header`,`home`,`ndate` FROM  news ORDER BY ndate DESC, news_id  DESC;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($news_id, $header,$text,$ndate)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($news_id, $header,$text,$ndate);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get a list of all news from table news 
 *
 * @return array array of records
 * @throws DBException
 */
function getAllNews($from) {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT `news_id`,`header`,`home`, `ntext`, `ndate` FROM news
	  WHERE news_id <= ?
	  ORDER BY ndate DESC, news_id  DESC LIMIT 5;')) {
		if (!$stmt->bind_param('i', $from)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($news_id, $header, $text, $fulltext, $ndate)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($news_id, $header, $text, $fulltext, $ndate);
			array_push($records, $record);
		}
		$stmt->free_result();
		$stmt->close();
		return $records;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get record by news_id from table news
 *
 * @return array array of values
 * @throws DBException
 */
function getByIDFromNews($news_id) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT `news_id`,`header`, `home`, `ntext`,`ndate` FROM  news WHERE news_id = ?;')) {
		if (!$stmt->bind_param('i', $news_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($id, $header, $text, $fulltext, $dt)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = array($id, $header, $text, $fulltext, $dt);
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * update a record into table biblio identifyed by record ID
 *
 * @param news_id  record id
 * @param string  header Header of news
 * @param string  text News text
* @return inserted record id
 * @throws DBException
 */
function updateNewsByID($news_id, $header, $text, $fulltext) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('UPDATE `news` SET `header`=?, `home`=?, `ntext`=?  WHERE `news_id`=?;')) {
		if (!$stmt->bind_param('sssi', $header, $text, $fulltext, $news_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * insert a new records into table biblio
 *
 * @param string  header Header of news
 * @param string  text News text
 * @return inserted record id
 * @throws DBException
 */
function news_insert_record($header, $text, $fulltext) {
	$db = UserConfig::getDB();
	$header = (!empty($header)) ? $header : NULL;
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO `news` (`header`, `home`, `ntext`, `ndate`) 
		VALUES (?, ?, ?, CURDATE())')) {
		if (!$stmt->bind_param('sss', $header, $text, $fulltext)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * get max id of news
 *
 * @return array array of records
 * @throws DBException
 */
function getMaxIDFromNews() {
	$db = UserConfig::getDB();
	$records = array();
	$record = null;
	if ($stmt = $db->prepare('SELECT MAX(news_id) FROM news;')) {
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($max)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = $max;
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 * get ip of the user
 *
 * @return ip
 */
function getIP() {
	$ip = null;
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
/**
 * count ip
 *
 * @return count
 * @throws DBException
 */
function countIP($ip) {
	$db = UserConfig::getDB();
	$record = null;
	if ($stmt = $db->prepare('SELECT count(ip) FROM selectedpoems WHERE ip=?')) {
		if (!$stmt->bind_param('s', $ip)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		if (!$stmt->bind_result($ip_count)) {
			throw new DBBindResultException($db, $stmt);
		}
		while ($stmt->fetch() === TRUE) {
			$record = $ip_count;
		}
		$stmt->free_result();
		$stmt->close();
		return $record;
	} else {
		throw new DBPrepareStmtException($db);
	}
}
/**
 *  a new records into table selectedpoems
 *
 * @param int  poems_id 
 * @param string  ip
 * @param string  CURRENT_TIMESTAMP
 * @return inserted record id
 * @throws DBException
 */
function votes_insert_record($poems_id, $ip) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	if ($stmt = $db->prepare('INSERT INTO `selectedpoems` (`poems_id`, `ip`) 
		VALUES (?, ?)')) {
		if (!$stmt->bind_param('is', $poems_id, $ip)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->insert_id;
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * delete record from table poems
 *
 * @param int  record_id 
 * @return affected_rows
 * @throws DBException
 */
function deleteRecordFromPoems($record_id) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	$record_id = trim($record_id);
	if ($stmt = $db->prepare('DELETE FROM `poems` WHERE `poems_id`=?')) {
		if (!$stmt->bind_param('i', $record_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}
/**
 * delete record from table originals
 *
 * @param int  record_id 
 * @return affected_rows
 * @throws DBException
 */
function deleteRecordFromOriginals($record_id) {
	$db = UserConfig::getDB();
	$r_id = NULL;
	$record_id = trim($record_id);
	if ($stmt = $db->prepare('DELETE FROM `originals` WHERE `poems_id`=?')) {
		if (!$stmt->bind_param('i', $record_id)) {
			throw new DBBindParamException($db, $stmt);
		}
		if (!$stmt->execute()) {
			throw new DBExecuteStmtException($db, $stmt);
		}
		$r_id = $stmt->affected_rows; // this works only if actual changes were made, if nothing changed return 0
		$stmt->close();
	} else {
		throw new DBPrepareStmtException($db);
	}
	return $r_id;
}

function maketopics($topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id) {
    $alltopics = array();
    if ($topic5_id) {
        list($topic_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic5_id);
        $topics = '<a href="./topics.php?action=show&record_id='.$topic_id.'" class="topics ref">'.$topic_name.'</a>';
        array_push($alltopics, $topics);
    }
    if ($topic4_id) {
        list($topic_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic4_id);
        $topics = '<a href="./topics.php?action=show&record_id='.$topic_id.'" class="topics ref">'.$topic_name.'</a>';
        array_push($alltopics, $topics);
    }
    if ($topic3_id) {
        list($topic_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic3_id);
        $topics = '<a href="./topics.php?action=show&record_id='.$topic_id.'" class="topics ref">'.$topic_name.'</a>';
        array_push($alltopics, $topics);
    }
    if ($topic2_id) {
        list($topic_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic2_id);
        $topics = '<a href="./topics.php?action=show&record_id='.$topic_id.'" class="topics ref">'.$topic_name.'</a>';
        array_push($alltopics, $topics);
    }
    if ($topic1_id) {
        list($topic_id,$topic_name,$topic_synonym, $topic_desc) = getTopicByID($topic1_id);
        $topics = '<a href="./topics.php?action=show&record_id='.$topic_id.'" class="topics ref">'.$topic_name.'</a>';
        array_push($alltopics, $topics);
    }
    $joinedTopics = join(" | ",$alltopics);
    return $joinedTopics;
}

function makeTranslator($translator1_id, $translator2_id) {
    list($junk, $tr_full_name, , , , , , , , , , ) = getByIDFromTranslators($translator1_id);
    $translator1 = '<a href="./translators.php?action=show&record_id='.$translator1_id.'">'.$tr_full_name.'</a>';
    if ($translator2_id) {
        list($junk, $tr2_full_name, , , , , , , , , , ) = getByIDFromTranslators($translator2_id);        
        $translator2 = '<a href="./translators.php?action=show&record_id='.$translator2_id.'">'.$tr2_full_name.'</a>';
        $translator = $translator1.', '. $translator2;
    }
    else {
        $translator = $translator1;
    }
    $translator = '<span class="translators">'.$translator.'</span>';
    return $translator;    
}
function makeAuthor($author_id){
    list($author_id, $full_name, $proper_name,  $dates,  $epoch, $present, $zh_trad, $zh_simple) = getByIDFromAuthors($author_id);
    $author = '<a href="./authors.php?action=show&record_id='.$author_id.'"><span class="author name">'.$proper_name.'</span>
    &nbsp;<span class="author dates">'.$dates.'</span></a>';
    if ($zh_trad) {
        $author .= '&nbsp;<span class="name zh">'.$zh_trad.'</span>';
    }
    else if ($zh_simple) {
        $author .= '&nbsp;<span class="name zh">'.$zh_simple.'</span>';
    }
    $author .= '&nbsp;<span class="epoch">'.$epoch.'</span>';
    return array($author, $proper_name,  $dates,  $epoch);
}
function makeCycle($cycle_ru,$cycle_zh,$translator_id)  {
    if ($cycle_ru && $cycle_zh) {
        $cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($cycle_zh).'">'.$cycle_zh.'</a></span> 
		<span class="cycle ru"><a href="/cycles.php?translator='.$translator_id.'&cycle='.urlencode($cycle_ru).'">'.$cycle_ru.'</a></span>';
    }
    elseif ($cycle_ru && !$cycle_zh) {
        $cycle = '<span class="cycle ru"><a href="/cycles.php?translator='.$translator_id.'&cycle='.urlencode($cycle_ru).'">'.$cycle_ru.'</a></span>';
    }
    else { $cycle = false; }
    return $cycle;
}
function makeSubCycle($subcycle_ru,$subcycle_zh,$translator_id)  {
    if ($subcycle_ru && $subcycle_zh) {
        $subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($subcycle_zh).'">'.$subcycle_zh.'</a></span> <span class="subcycle ru"><a href="/cycles.php?translator='.$translator_id.'&subcycle='.urlencode($subcycle_ru).'">'.$subcycle_ru.'</a></span>';
    }
    elseif($subcycle_ru && !$subcycle_zh){
        $subcycle = '<span class="subcycle ru"><a href="/cycles.php?translator='.$translator_id.'&subcycle='.urlencode($subcycle_ru).'">'.$subcycle_ru.'</a></span>';
    }
    else { $subcycle = false; }
    return $subcycle;
} 
function makeFinalArray ($records) {
    $new_arr = array();
    $arrAuthors = array();
    $final = array();
    $author ='';
    for ($i=0; $i < count($records) ; $i++) {
        list($poems_id,$author_id,$translator1_id,$translator2_id,
        $topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
        $poem_name_zh,$poem_name_ru,$poem_code,$biblio_id) = $records[$i];
        list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
        $author = $author_html;
        if ($translator1_id) {
            $translator = makeTranslator($translator1_id, $translator2_id);
        }
        else {
            $translator = '';
        }
        array_push($arrAuthors, $author);
        if (array_key_exists($author, $new_arr)) {
            array_push($new_arr[$author],  $records[$i]); 
        }
        else {
            $new_arr[$author] = array($records[$i]);
        }    
    }
    $arrAuthors = array_unique($arrAuthors);
    foreach ($arrAuthors as  $author) {
        $cycles = array();
        $poems = array();
        for ($i=0; $i < count($new_arr[$author]) ; $i++) {
            $poem = $new_arr[$author][$i];
			if ($translator) {
//				echo $translator;
				preg_match('/record_id=(\d+)">/',$translator,$match);
				preg_match('/\d+/',$match[0], $match1);
				$translator_id = $match1[0];
	#            $poem[2] = makeTranslator($poem[2], $poem[3]);
				$cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($poem[9]).'">'.$poem[9].'</a></span><span class="cycle ru"><a href="/cycles.php?translator='.$translator_id.'&cycle='.urlencode($poem[10]).'">'.$poem[10].'</a></span>';
				$subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($poem[11]).'">'.$poem[11].'</a></span> <span class="subcycle ru"><a href="/cycles.php?translator='.$translator_id.'&subcycle='.urlencode($poem[12]).'">'.$poem[12].'</a></span>';
			}
			else {
//				echo 'no translator';
				$cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($poem[9]).'">'.$poem[9].'</a></span> <span class="cycle ru">'.$poem[10].'</span>';
				$subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($poem[11]).'">'.$poem[11].'</a></span> <span class="subcycle ru">'.$poem[12].'</span>';
			}
			if (((strpos($cycle, 'cycle_zh=">') > 0) && (strpos($cycle, 'cycle=">') > 0)) || ((strpos($cycle, 'cycle zh"></span>') > 0) && (strpos($cycle, 'cycle ru"></span>') > 0))) {
				$cycle = 'default'.$i;
			}			
			if (((strpos($subcycle, 'subcycle_zh=">') > 0) && (strpos($subcycle, 'subcycle=">') > 0)) || ((strpos($subcycle, 'subcycle zh"></span>') > 0) && (strpos($subcycle, 'subcycle ru"></span>') > 0))) {
                $subcycle = 'default'.$i;
            }
            if (!array_key_exists($cycle, $poems)) {
                $poems[$cycle] = array();
            }
            if (!array_key_exists($subcycle, $poems[$cycle])) {
                $poems[$cycle][$subcycle] = array();
            }
            array_push($poems[$cycle][$subcycle], $poem);
        }
        array_push($final, array('author' => $author, 'poems' => $poems));
    }
    return $final;
}

function makeFinaTranslatorslArray ($records) {
    $new_arr = array();
    $arrTranslators = array();
    $final = array();
    $author ='';
    for ($i=0; $i < count($records) ; $i++) {
        list($poems_id,$author_id,$translator1_id,$translator2_id,
        $topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
        $poem_name_zh,$poem_name_ru,$poem_code,$biblio_id) = $records[$i];
        list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
        $author = $author_html;
        $translator = makeTranslator($translator1_id, $translator2_id);
        array_push($arrTranslators, $translator);
        if (array_key_exists($translator, $new_arr)) {
            array_push($new_arr[$translator],  $records[$i]); 
        }
        else {
            $new_arr[$translator] = array($records[$i]);
        }    
    }
    $arrTranslators = array_unique($arrTranslators);
    foreach ($arrTranslators as  $translator) {
        preg_match('/id=\d+">(.+?)<\/a>/',$translator,$match);
        $unsorted[$match[1]] = $translator;
    }
    ksort($unsorted);
    $sorted = [];
    foreach ($unsorted as  $translator) {
        array_push($sorted, $translator);
    }
//print_r($sorted);
    foreach ($sorted as  $translator) {
        $cycles = array();
        $poems = array();
        for ($i=0; $i < count($new_arr[$translator]) ; $i++) {
            $poem = $new_arr[$translator][$i];
//			$translator_id = getTranslatorIDByName($translator);
			preg_match('/record_id=(\d+)">/',$translator,$match);
			preg_match('/\d+/',$match[0], $match1);
			$translator_id = $match1[0];
            $cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($poem[9]).'">'.$poem[9].'</a></span> <span class="cycle ru"><a href="/cycles.php?translator='.$translator_id.'&cycle='.urlencode($poem[10]).'">'.$poem[10].'</a></span>';
            $subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($poem[11]).'">'.$poem[11].'</a></span> <span class="subcycle ru"><a href="/cycles.php?translator='.$translator_id.'&subcycle='.urlencode($poem[12]).'">'.$poem[12].'</a></span>';
			if (((strpos($cycle, 'cycle_zh=">') > 0) && (strpos($cycle, 'cycle=">') > 0)) || ((strpos($cycle, 'cycle zh"></span>') > 0) && (strpos($cycle, 'cycle ru"></span>') > 0))) {
				$cycle = 'default'.$i;
			}			
			if (((strpos($subcycle, 'subcycle_zh=">') > 0) && (strpos($subcycle, 'subcycle=">') > 0)) || ((strpos($subcycle, 'subcycle zh"></span>') > 0) && (strpos($subcycle, 'subcycle ru"></span>') > 0))) {
                $subcycle = 'default'.$i;
            }
            if (!array_key_exists($cycle, $poems)) {
                $poems[$cycle] = array();
            }
            if (!array_key_exists($subcycle, $poems[$cycle])) {
                $poems[$cycle][$subcycle] = array();
            }
            array_push($poems[$cycle][$subcycle], $poem);
        }
        array_push($final, array('translator' => $translator, 'poems' => $poems));
    }
    return $final;
}
function makeFinalArraybyTopicSearch ($records) {
//to not make links for cycles  and subcycles
    $new_arr = array();
    $arrAuthors = array();
    $final = array();
    $author ='';
    for ($i=0; $i < count($records) ; $i++) {
        list($poems_id,$author_id,$translator1_id,$translator2_id,
        $topic1_id,$topic2_id,$topic3_id,$topic4_id,$topic5_id,$cycle_zh,$cycle_ru,$subcycle_zh,$subcycle_ru,
        $poem_name_zh,$poem_name_ru,$poem_code,$biblio_id) = $records[$i];
        list($author_html, $proper_name,  $dates,  $epoch) = makeAuthor($author_id);
        $author = $author_html;
        if ($translator1_id) {
            $translator = makeTranslator($translator1_id, $translator2_id);
        }
        else {
            $translator = '';
        }
        array_push($arrAuthors, $author);
        if (array_key_exists($author, $new_arr)) {
            array_push($new_arr[$author],  $records[$i]); 
        }
        else {
            $new_arr[$author] = array($records[$i]);
        }    
    }
    $arrAuthors = array_unique($arrAuthors);
    foreach ($arrAuthors as  $author) {
        $cycles = array();
        $poems = array();
        for ($i=0; $i < count($new_arr[$author]) ; $i++) {
            $poem = $new_arr[$author][$i];
			if ($translator) {
//				echo $translator;
				preg_match('/record_id=(\d+)">/',$translator,$match);
				preg_match('/\d+/',$match[0], $match1);
				$translator_id = $match1[0];
	#            $poem[2] = makeTranslator($poem[2], $poem[3]);
				$cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($poem[9]).'">'.$poem[9].'</a></span><span class="cycle ru">'.$poem[10].'</span>';
				$subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($poem[11]).'">'.$poem[11].'</a></span> <span class="subcycle ru">'.$poem[12].'</span>';
			}
			else {
//				echo 'no translator';
				$cycle = '<span class="cycle zh"><a href="/cycles.php?cycle_zh='.urlencode($poem[9]).'">'.$poem[9].'</a></span> <span class="cycle ru">'.$poem[10].'</span>';
				$subcycle = '<span class="subcycle zh"><a href="/cycles.php?subcycle_zh='.urlencode($poem[11]).'">'.$poem[11].'</a></span> <span class="subcycle ru">'.$poem[12].'</span>';
			}
			if (((strpos($cycle, 'cycle_zh=">') > 0) && (strpos($cycle, 'cycle=">') > 0)) || ((strpos($cycle, 'cycle zh"></span>') > 0) && (strpos($cycle, 'cycle ru"></span>') > 0))) {
				$cycle = 'default'.$i;
			}			
			if (((strpos($subcycle, 'subcycle_zh=">') > 0) && (strpos($subcycle, 'subcycle=">') > 0)) || ((strpos($subcycle, 'subcycle zh"></span>') > 0) && (strpos($subcycle, 'subcycle ru"></span>') > 0))) {
                $subcycle = 'default'.$i;
            }
            if (!array_key_exists($cycle, $poems)) {
                $poems[$cycle] = array();
            }
            if (!array_key_exists($subcycle, $poems[$cycle])) {
                $poems[$cycle][$subcycle] = array();
            }
            array_push($poems[$cycle][$subcycle], $poem);
        }
        array_push($final, array('author' => $author, 'poems' => $poems));
    }
    return $final;
}
