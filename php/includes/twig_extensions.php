<?php

$twig = new Twig_Environment($loader, array(
    'debug' => true
));

$twig->addExtension(new Twig_Extension_Debug());

//$twig->getExtension('core')->setTimezone('Europe/Rome');

$twig->addExtension(new Twig_Extensions_Extension_Intl());
$twig->addExtension(new Twig_Extensions_Extension_Array());
$twig->addExtension(new Twig_Extensions_Extension_Text());

/*
// richiede
// composer require erusev/parsedown
$filter = new Twig_SimpleFilter('md2html', function ($string) {
    $Parsedown = new Parsedown();
	echo $Parsedown->text($string);
});
$twig->addFilter($filter);
*/

// cssinliner
use Twig\Extra\CssInliner\CssInlinerExtension;
$twig->addExtension(new CssInlinerExtension());

$filter = new Twig_SimpleFilter('localizednumber', function ($number) {
    return number_format($number, 2, ',', '.');
});
$twig->addFilter($filter);


$filter = new Twig_SimpleFilter('json_decode', function ($string) {
    return json_decode($string, true);
});
$twig->addFilter($filter);

$function = new Twig_SimpleFunction('absolute_url', function ($arg, $option1=null, $option2=null) {

    return $arg;
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('path', function ($url, $option1=null, $option2=null) {
    if(substr($url, 0, 1) != '/') {
	    $url = '/' . $url;
    }
    return $url;
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('asset', function ($url) {
    if(substr($url, 0, 1) != '/') {
	    $url = '/' . $url;
    }
    return $url;
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('controller', function () {
	return '';
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('render', function () {
	return '';
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('is_granted', function ($role) {
	return true;
});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('class', function ($class) {
	return "class_" . (is_array($class) ? join('_', $class) : $class);
});
$twig->addFunction($function);

/*
function twig_include_raw(Twig_Environment $env, $template) {
    return $env->getLoader()->getSource($template);
}
$twig->addFunction('include_raw', new Twig_SimpleFunction('twig_include_raw', array('needs_environment' => true, 'is_safe'=> array('all'))));
*/

$filter = new Twig_SimpleFilter('humanize', function ($string) {
  return $string;
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('trans', function ($string) {
    return $string;
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('format_file_from_text', function ($string) {
    return $string;
});
$twig->addFilter($filter);


// utility PHP
// Transforms an under_scored_string to a camelCasedOne
function camelize($scored) {
return lcfirst(
  implode(
    '',
    array_map(
      'ucfirst',
      array_map(
        'strtolower',
        explode(
          '_', $scored)))));
}

