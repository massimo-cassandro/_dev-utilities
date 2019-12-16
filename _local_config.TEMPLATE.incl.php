<?php
// copiare nella dir _TEST eliminando TEMPLATE dal nome

$bundle_root = $_SERVER['DOCUMENT_ROOT'] . '/__BUNDLE_ROOT__/Resources/views';
$framework = 'bootstrap4';
// widget form personalizzati
//$form_widget_include = $_SERVER['DOCUMENT_ROOT'] . '/_TEST/twig_form_widgets.incl.php';

/* DEFAULT
// directory che contiene gli include php con le variabili di test globali
$glob_test_vars_dir = '/_TEST'; // senza slash finale
// directory che contiene gli include php con le variabili di test per le varie pagine
$pages_test_vars_dir = "{$glob_test_vars_dir}_pagine/"; // con slash finale
*/

$views_folders_array= array(); // non necessaria se tutti i template sono in $bundle_root

$registered_namespaces = array (
  'views' => $_SERVER['DOCUMENT_ROOT']. '/__BUNDLE_ROOT__/Resources/views',
  //'includes' => $_SERVER['DOCUMENT_ROOT']. '/__BUNDLE_ROOT__/Resources/views/_templates/includes'
);

//$test_variables = array();
//$extra_twig_filters = array();

