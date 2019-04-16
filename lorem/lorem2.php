<?php
require  'vendor/joshtronic/php-loremipsum/src/LoremIpsum.php';
/*

  Estende la classe `joshtronic\LoremIpsum()` (vedi <https://github.com/joshtronic/php-loremipsum>).

  USO:
  -------------
  require_once $_SERVER['DOCUMENT_ROOT'] . '/_dev-utilities/lorem/lorem2.php';
  $lorem = new lorem2();


  $lorem->string(120, 560, true)                 → stringa di lunghezza indicata con possibilità di minimo, massimo e null
  $lorem->date('2018-01-01', '2018-03-16', true) → data random con possibilità null


  $lorem->num(100, 1000, true)                   → numero tra i valori minimo e massimo indicati, con possibilità null
                                                  ( anche mt_rand(min,max) ). Arrotonda alla decina

  array img random (come da db file) per local_viewer:
  canBeNull: default false
  maxW, maxH -> se null, larghezza fissa sul valor min

  $lorem->img_array(minW, maxW, minH, maxH, canBeNull: false|true, 'extra_params', src: 'gd|placeholder|picsum')

  $lorem->img_array(minW, maxW, minH, maxH, false, '', 'placeholder')
  $lorem->img_array(minW, maxW, minH, maxH, false, '', 'picsum')
  $lorem->img_array(minW, maxW, minH, maxH) // gd, default
*/
class lorem2 extends joshtronic\LoremIpsum
{
  public function string($min=150, $max=255, $canBeNull = false) {
    if( $canBeNull ) {
      if( mt_rand(0,1) ) return null;
    }
    $string_max_length = mt_rand($min, $max);

    while (true) {
      $words = $this->wordsArray(round($max/4, 0, PHP_ROUND_HALF_UP) +1, false);
      $string = implode(' ', $words);
      if( strlen($string) >= $min) {
        break;
      }
    }

    while(true) {
      if(strlen($string) <= $string_max_length) {
        break;
      } else {
        array_pop($words);
        $string = implode(' ', $words);
      }
    }

    return ucfirst(trim($string));
  }

  public function date($min='1900-01-01', $max='1965-13-16', $canBeNull = false) {

    if( $canBeNull ) {
      if( mt_rand(0,1) ) return null;
    }

    $min_timestamp = strtotime($min);
    $max_timestamp = strtotime($max);
    $random_timestamp = mt_rand($min_timestamp, $max_timestamp);

    return date("Y-m-d",$random_timestamp);

  }

  public function num( $min = 0, $max= 1000, $canBeNull = false, $round = true) {
    if( $canBeNull ) {
      if( mt_rand(0,1) ) return null;
    }

    $num = mt_rand($min, $max);
    if($round) $num = ceil($num / 10) * 10;
    return $num ;
  }

  public function img_array(
    $minWidth = 100,
    $maxWidth = 800,
    $minHeight = 100,
    $maxHeight = 800,
    $canBeNull = false,
    $extraParams= '',
    $nobb      = false, // se true no stampa il valore bb sull'immagine
    $src= 'gd'  // $src -> placeholder o picsum o gd
  ) {

    if( $canBeNull ) {
      if( mt_rand(0,1) ) return null;
    }

    $width = $maxWidth? mt_rand($minWidth, $maxWidth) : $minWidth;
    $height = $maxHeight? mt_rand($minHeight, $maxHeight) : $minHeight;
    $src = strtolower($src);

    $imgId = array(
      'src|' . $src,
      'w|'   . $width,
      'h|'   . $height
    );

    if ($extraParams)  $imgId[] = "extra|{$extraParams}";
    if ($nobb) $imgId[] = "nobb|1";


    return array(
      'id' => 'v:' . implode(',', $imgId), // questa forma non richiede urlencoding ed è leggibile
      'width' => $width,
      'height' => $height,
      'mime' => $src == 'gd'? 'image/png' : 'image/jpeg'
    );
  }

  public function local_img_array($img_local_path, $nobb = true, $concat_array=null) {

    $img_info = getimagesize($_SERVER['DOCUMENT_ROOT'] . $img_local_path, $nobb);

    $img_array= array (
      'id' => urlencode(urlencode($img_local_path . ($nobb  ? '|nobb' : ''))),
      'width' => $img_info[0],
      'height' => $img_info[1],
      'mime' => $img_info['mime']
    );

    if($concat_array) {
      $img_array = array_merge($img_array, $concat_array);
    }

    return $img_array;

  }
}
