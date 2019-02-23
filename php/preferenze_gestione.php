<?php

// DB
require_once $_SERVER['DOCUMENT_ROOT'] . '/_TEST/db.incl.php';


$query = "SELECT s.sezione, s.intro, p.*
	FROM preferenze AS p
	LEFT JOIN preferenze_sezioni AS s ON (s.id = p.sezione_id)
	ORDER BY s.ordine, s.sezione, p.ordine";


$result = $dbi->query($query);


$prefs = array();
while ($row = $result->fetch_assoc()) {

	$temp=array();
	foreach($row as $k => $v) {
		$temp[underscoreToCamel($k)] = $v;
	}
	$prefs[]=$temp;
}

$dev = array(
	'entity' => $prefs
);


//echo '<pre>'; print_r($dev); echo '</pre><hr>';
