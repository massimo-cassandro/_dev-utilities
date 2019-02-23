<?php
// per includere questo script:
// require_once $_SERVER['DOCUMENT_ROOT'] . '/_TEST/db.incl.php';


define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PWD', 'mysqlmax');
$db = 'xxxx';


$dbi = new mysqli(DB_HOST, DB_USER, DB_PWD, $db);
$dbi->set_charset("utf8");


function underscoreToCamel ($str) {
  // Remove underscores, capitalize words, squash, lowercase first.
  return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
}

// data types
$data_types=[];
function get_data_types ($table){
	global $dbi, $data_types;

	/*
		COLUMN_TYPE → tipo colonna con lunghezza (es varchar(255))
		DATA_TYPE   → solo tipo colonna (es. varchar)
	*/

	$query = "SELECT COLUMN_NAME, COLUMN_TYPE, DATA_TYPE
		FROM information_schema.COLUMNS
		WHERE TABLE_NAME='{$table}'";


	$result = $dbi->query($query);

	while ($row = $result->fetch_assoc()) {
		$data_types['ordini'][$row['COLUMN_NAME']] = $row['DATA_TYPE'];
	}

}
