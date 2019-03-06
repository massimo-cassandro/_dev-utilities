<?php

//preferenze
$query = "SELECT p.id, p.key, p.val
  FROM ada_app.preferenze as p, ada_app.preferenze_sezioni as s
  WHERE s.preferenze_aree_id = 7
    AND p.preferenze_sezioni_id = s.id";


$result = $dbi->query($query);

if(!isset($dev)) $dev = array();

$test_variables['preferenze'] = array();
while ($row = $result->fetch_assoc()) {

	$test_variables['preferenze'][$row['key']]=$row['val'];
}
//echo '<pre>'; print_r($test_variables['preferenze']); echo '</pre><hr>';
