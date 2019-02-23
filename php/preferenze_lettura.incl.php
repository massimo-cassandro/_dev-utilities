<?php
//NB includere dopo l'impostazione di $dev


// DB
require_once $_SERVER['DOCUMENT_ROOT'] . '/_TEST/db.incl.php';

$query = "SELECT `id`, `key`, `val` FROM preferenze";
$result = $dbi->query($query);

if(!isset($dev)) $dev = array();

$dev['preferenze'] = array();
while ($row = $result->fetch_assoc()) {

	$dev['preferenze'][$row['key']]=$row['val'];
}

//echo '<pre>'; print_r($preferenze); echo '</pre><hr>';

// stampa array se non si vuole attivare il db
//echo '$dev[\'preferenze\'] = json_decode(\'' . str_replace("'", "\'", json_encode($dev['preferenze'])) . '\', true);';

