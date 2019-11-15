<?php
$bundle_root = null; //$_SERVER['DOCUMENT_ROOT'] . '/__BUNDLE_ROOT__';

/* DEFAULT
// directory che contiene gli include php con le variabili di test globali
$glob_test_vars_dir = '/_TEST'; // senza slash finale
// directory che contiene gli include php con le variabili di test per le varie pagine
$pages_test_vars_dir = "{$glob_test_vars_dir}_pagine/"; // con slash finale
*/

$views_folders_array= array(); // non necessaria se tutti i template sono in $bundle_root

$registered_namespaces = array (
  'views' => $bundle_root . '/Resources/views',
  //'includes' => $bundle_root . '/Resources/views/_templates/includes'
);

$form_template = '@views/Form/bootstrap_4_layout.html.twig';

//$test_variables = array();
//$extra_twig_filters = array();

