<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('default_charset', 'UTF-8');

date_default_timezone_set('Europe/Rome');

putenv('LC_ALL=it_IT');
setlocale(LC_ALL, 'it_IT', 'it', 'IT');

if ( empty($_GET['twig']) ) die( 'Twig non indicato' );

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// ** FILE DI CONFIGURAZIONE LOCALE **
/*
  eventuale file di configurazione aggiuntivo specifico del progetto
  facoltativo
  se presente deve istanziare queste variabili:

  $bundle_root         → root del bundle symfony (default: null)

  $views_folders_array → array con i path delle cartelle views in cui twig deve cercare i template
               se non presenti, a questo array vengono aggiunte sempre la root del sito locale
               e la root del bundle
               (default → array con solo la root del sito locale)

  $test_variables      → eventuale set di variabili da passare ogni volta al twig
                         (default: set di variabili definiti di seguito)

  $global_test_dir → percorso della dir contenente i file con le variabili di test globali
                         (default `/_TEST`)

  $pages_test_dir → percorso della dir contenente i file con le variabili di test delle singole pagine
                         (default `/{$global_test_dir}/_pagine/`) NB con slash finale
*/

if(empty($global_test_dir )) $global_test_dir = "/_TEST";
if(empty($pages_test_dir )) $pages_test_dir = "{$global_test_dir}/_pagine/"; // con slash finale


if(file_exists($_SERVER['DOCUMENT_ROOT'] . "{$global_test_dir}/twig_render_config.php") ) {
  require $_SERVER['DOCUMENT_ROOT'] . "{$global_test_dir}/twig_render_config.php";
}

//default
if(empty($bundle_root)) $bundle_root = null;
if(empty($views_folders_array)) $views_folders_array = array();
if(!in_array($_SERVER['DOCUMENT_ROOT'], $views_folders_array)) $views_folders_array[] = $_SERVER['DOCUMENT_ROOT'];
if($bundle_root and !in_array($bundle_root, $views_folders_array)) $views_folders_array[] = $bundle_root;

$loader = new Twig_Loader_Filesystem( $views_folders_array );

require_once 'twig_extensions.php';
require_once 'form_renderer.php';

// registered namespaces
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
  (in {$pages_test_dir} anziché in views) del twig

  $pages_test_dir va definito, se necessario in _local_config.incl.php
  $pages_test_dir è il percorso dalla root del sito (con slash finale)

  come nel caso precedente, tutte le variabili vanno inserite in un file php
  che abbia lo stesso nome del file twig (estensioni escluse) all'interno di
  un array $dev
  tutte le chiavi dell'array verranno aggiunte a $test_variables
*/
$test_file = false;
if(file_exists($_SERVER['DOCUMENT_ROOT']. $pages_test_dir . $twig_file . '.incl.php')) {
  $test_file = $_SERVER['DOCUMENT_ROOT']. $pages_test_dir . $twig_file . '.incl.php';

} else if( file_exists($_SERVER['DOCUMENT_ROOT']. $pages_test_dir . $twig_file . '.php') ) {
  $test_file = $_SERVER['DOCUMENT_ROOT']. $pages_test_dir . $twig_file . '.php';
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

// aggiunta eventuale file js per l'ambiente di sviluppo globale
$global_dev_js_file = $global_test_dir . '/global.dev.js';
if(file_exists($_SERVER['DOCUMENT_ROOT']. $global_dev_js_file )) {
  $global_dev_js_file = '<script src="' . $global_dev_js_file . '"></script>';
  $template_code = str_replace('</body>', "\n${global_dev_js_file}\n</body>" ,$template_code );
}

// aggiunta eventuale file js per l'ambiente di sviluppo legato ad una pagina specifica
$dev_js_file = $pages_test_dir . $twig_file . '.dev.js';
if(file_exists($_SERVER['DOCUMENT_ROOT']. $dev_js_file )) {
  $dev_js_file = '<script src="' . $dev_js_file . '"></script>';
  $template_code = str_replace('</body>', "\n${dev_js_file}\n</body>" ,$template_code );
}

//echo htmlspecialchars($template_code); exit;

echo $template_code;
