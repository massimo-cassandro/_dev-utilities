<?php
// copiare nella dir _TEST eliminando TEMPLATE dal nome

$bundle_root = $_SERVER['DOCUMENT_ROOT'] . '/__BUNDLE_ROOT__/Resources/views';
$framework = 'bootstrap4';
// widget form personalizzati
//$form_widget_include = $_SERVER['DOCUMENT_ROOT'] . '/_TEST/twig_form_widgets.incl.php';

// directory che contiene gli include php con le variabili di test per le varie pagine
$test_vars_dir = '/_TEST/test_vars/'; // con slash finale

$views_folders_array= array(); // non necessaria se tutti i template sono in $bundle_root

$registered_namespaces = array (
  'views' => $_SERVER['DOCUMENT_ROOT']. '/__BUNDLE_ROOT__/Resources/views',
  //'includes' => $_SERVER['DOCUMENT_ROOT']. '/__BUNDLE_ROOT__/Resources/views/_templates/includes'
);

//$test_variables = array();
//$extra_twig_filters = array();

