<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('default_charset', 'UTF-8');

date_default_timezone_set('Europe/Rome');

putenv('LC_ALL=it_IT');
setlocale(LC_ALL, 'it_IT', 'it', 'IT');

if ( empty($_GET['twig']) ) die( 'Twig non indicato' );

require_once $_SERVER['DOCUMENT_ROOT'] . '/twig/vendor/autoload.php';

// ** FILE DI CONFIGURAZIONE LOCALE **
/*
  eventuale file di configurazione aggiuntivo specifico del progetto
  facoltativo
  se presente deve istanziare queste variabili:

  $bundle_root         → root del bundle symfony (default: null)

  $views_folders_array → array con i path delle cartelle views in cui twiog deve cercare i template
               se non presenti, a questo array vengono aggiunte sempre la root del sito locale
               e la root del bundle
               (default → array con solo la root del sito locale)

  $framework           → framework front-end utilizzato (tra bootstrap3, bootstrap4, foundation6)
               (default: boostrap4)

  $test_variables      → eventuale set di variabili da passare ogni volta al twig
                         (default: set di variabili definiti di seguito)

  $test_vars_dir       → percorso della dir contenente i file con le varibili di test
                         (default `/_TEST/`)
*/
if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/_TEST/_local_config.incl.php') ) {
  require $_SERVER['DOCUMENT_ROOT'] . '/_TEST/_local_config.incl.php';
}

//default
if(empty($bundle_root)) $bundle_root = null;
if(empty($framework)) $framework = 'bootstrap4';
if(empty($views_folders_array)) $views_folders_array = array();
if(!in_array($_SERVER['DOCUMENT_ROOT'], $views_folders_array)) $views_folders_array[] = $_SERVER['DOCUMENT_ROOT'];
if($bundle_root and !in_array($bundle_root, $views_folders_array)) $views_folders_array[] = $bundle_root;

$loader = new Twig_Loader_Filesystem( $views_folders_array );

require_once 'includes/twig_extensions.php';

// namespaces
/*
  esempio (in local_config):

  $registered_namespaces = array (
    'views' => $_SERVER['DOCUMENT_ROOT']. '/AdaAppBundle/Resources/views'
  );
*/
if(!empty($registered_namespaces)) {
  foreach( $registered_namespaces as $k => $v ) {
    $loader->addPath( $v, $k);
  }
}


// includes dei widget per i form
if(!empty($form_widget_include)) {
  require_once $form_widget_include;
} else {
  require_once 'includes/twig_form_funcs.incl.php';
}

//require_once 'includes/twig_form_funcs_OLD.incl.php';

if(isset($extra_twig_filters)) {
  foreach ($extra_twig_filters as $v ) {
    $filter = new Twig_SimpleFilter($v, function ($str) {
        return $str;
    });
    $twig->addFilter($filter);
  }
}

//echo $_GET['twig']; exit;
//echo '<pre>'; print_r($_GET); echo '</pre><hr>'; exit;


if(!isset($test_variables)) $test_variables=array();

$test_variables = array_merge(array(
  'error'  => array(),
  'is_mdev'  => true,
  'test'   => true,
  'local_test'  => true

/*
  'app' => array(
    'user' => array (
      'ruolo_id' => 1,
      'role' => 'ROLE_SUPER_ADMIN',
      'nome' => 'Massimo',
      'locale' => 'it',
    )
  )
*/
), $test_variables);


// default app.user (se non presente in _local_config.incl.php)
if( !isset( $test_variables['app'] ) ) {
  $test_variables = array_merge(array(
    'app' => array(
      'user' => array (
        'ruolo_id' => 1,
        'role' => 'ROLE_SUPER_ADMIN',
        'nome' => 'Massimo',
        'locale' => 'it',
      )
    )
  ), $test_variables);
}


// tipizzazione dei campi ricorrenti in ogni progetto
$test_variables['form'] = array(

  //'chiave_campo' => array('type' => 'text checkbox select date time textarea'),
  'cancellato'   => array('type' => 'checkbox'),
  'cancellata'   => array('type' => 'checkbox'),
  'annullata'    => array('type' => 'checkbox'),
  'annullato'    => array('type' => 'checkbox'),
  'pubblica'     => array('type' => 'checkbox'),
  '_token'       => array('type' => 'hidden'),
  'attivato'     => array('type' => 'checkbox'),
  'attivata'     => array('type' => 'checkbox')
);


/*
  Percorso del file twig (esclusa estensione `.html.twig`)
  $_GET['twig'] è prodotto da .htaccess
*/
$twig_file = $_GET['twig'];
unset($_GET['twig']);

/*
eventuali parametri di test aggiuntivi
vanno inseriti in un file php che contenga un array $dev
tutte le chiavi dell'array verranno aggiunte a $test_variables
caricato da load_dev_params (js)
*/
if(!empty($_GET['dev'])) {
  include $_SERVER['DOCUMENT_ROOT']. $_GET['dev'];

  foreach($dev as $k => $v) { // $dev è contenuto in $_SERVER['DOCUMENT_ROOT']. $_GET['dev']
    $test_variables[$k] = $v;
  }
  unset($_GET['dev']);
}

/*
  inclusione dati da un file che abbia lo stesso nome e lo stesso path
  (in {$test_vars_dir} anziché in views) del twig

  $test_vars_dir va definito, se necessario in _local_config.incl.php
  $test_vars_dir è il percorso dalla root del sito (con slash finale)

  come nel caso precedente, tutte le variabili vanno inserite in un file php
  che abbia lo stesso nome del file twig (estensioni escluse) all'interno di
  un array $dev
  tutte le chiavi dell'array verranno aggiunte a $test_variables
*/
if(empty($test_vars_dir )) $test_vars_dir = "/_TEST/";
$test_file = false;
if(file_exists($_SERVER['DOCUMENT_ROOT']. $test_vars_dir . $twig_file . '.incl.php')) {
  $test_file = $_SERVER['DOCUMENT_ROOT']. $test_vars_dir . $twig_file . '.incl.php';

} else if( file_exists($_SERVER['DOCUMENT_ROOT']. $test_vars_dir . $twig_file . '.php') ) {
  $test_file = $_SERVER['DOCUMENT_ROOT']. $test_vars_dir . $twig_file . '.php';
}



if($test_file) {
  include  $test_file;
  //$test_variables = array_merge_recursive( $test_variables, $dev);
  $test_variables = array_replace_recursive( $test_variables, $dev);
}

// eventuali ulteriori variabili get vengono passate al file twig così come sono (ad esempio fancy)
foreach($_GET as $k => $v) {
  if($k == 'fancy') $v = 1;
  $test_variables[$k] = $v;
}
/*
echo "--{$twig_file}--<br>";
echo "--{$test_file}--<br>";
echo '<pre>'; print_r($test_variables); echo '</pre><hr>';exit();
*/

$template_code = $twig->render("{$twig_file}.html.twig", $test_variables);

// aggiounta eventuale file js per l'ambiente di sviluppo
$dev_js_file = $test_vars_dir . $twig_file . '.dev.js';
if(file_exists($_SERVER['DOCUMENT_ROOT']. $dev_js_file )) {
  $dev_js_file = '<script src="' . $dev_js_file . '"></script>';
  $template_code = str_replace('</body>', "\n${dev_js_file}\n</body>" ,$template_code );
}

//echo htmlspecialchars($template_code); exit;

echo $template_code;
