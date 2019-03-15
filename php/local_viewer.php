<?php
/*
  local viewer immagini per ambiente di sviluppo
  questo include deve essere richiamato da uno script che imposti la variabile $img_file
  oppure chiamato direttamente aggiungendo il parametro get `img_id`

  Immagine picsum dimensioni random:
  $_GET['img_id'] = 'picsum|' . mt_rand(800, 1200) . '/' . mt_rand(600, 1200)

  con $lorem
  $lorem->img_array(800, 1200, 600, 1000, false, '', 'picsum')
  $lorem->img_array(800, 1200, 600, 1000, false, '', 'placeholder') // placeholder
  $lorem->img_array(800, 1200, 600, 1000); // local GD, canBeNull == false

*/

//*******
$font = $_SERVER['DOCUMENT_ROOT'] . '/_dev-utilities/php/CANDARAB.TTF';
//******


if(!empty($_GET['img_id'])) $img_file = $_GET['img_id'];

if( substr($img_file, 0, 2) == 'v:') {
  list($temp, $img_file) = explode(':', $img_file );

  $temp = explode(',', $img_file);
  $img_params= array();
  foreach ($temp as $p) {
    list($k, $v) = explode('|', $p);
    $img_params[$k] = $v;
  }

  $img_params['src'] = strtolower($img_params['src']);
}


if($img_params['src'] == 'picsum')  { // picsum|width/height (picsum|400/500)

  $img_file = 'https://picsum.photos/' . $img_params['w'] . '/' . $img_params['h'] . '/?random';

} else if($img_params['src'] ==  'placeholder') { // placeholder|350x150 | placeholder|__params__

  $img_file =  'https://via.placeholder.com/' . $img_params['w'] . 'x' . $img_params['h'] .'.jpeg';

}

function calculate_text_sizes($string, $img_width, $img_height) {
  global $font;

  $font_size = 14;
  $bbox = imagettfbbox($font_size, 0, $font, $string);
  $textBoxWidth = $bbox[2]; // larghezza del testo

  // fattore di ridimensionamento del testo
  $factor = round(($img_width / $textBoxWidth), 0, PHP_ROUND_HALF_DOWN);
  $font_size = ($font_size * $factor);

  $font_size -= $font_size > 40 ? 10 : 0;

  // ricalcolo textbox
  $bbox = imagettfbbox($font_size, 0, $font, $string);
  $x = ($img_width - $bbox[2]) / 2;
  $y = ($img_height - ($bbox[1] + $bbox[7])) / 2;

  return array(
    'fontsize' => $font_size,
    'x' => $x,
    'y' => $y,
    'w' => $bbox[2],
    'h' => $bbox[1] + $bbox[7],
    'bbox' => $bbox
  );
}


if( $img_file ) {

  if($img_params['src'] == 'gd') {
    $isGD = true;


    $original_width = $img_params['w'];
    $original_height = $img_params['h'];

    $im = @imagecreate($original_width, $original_height);
    $background_color = imagecolorallocate($im, 200, 200, 200);
    $text_color = imagecolorallocate($im, 140, 140, 140);

    $string = "{$original_width}×{$original_height}";
    $text_sizes = calculate_text_sizes($string , $original_width, $original_height);
    imagettftext(
      $im,
      $text_sizes['fontsize'],
      0,
      $text_sizes['x'],
      (empty($_GET['bb']) ? $text_sizes['y'] : $text_sizes['fontsize'] + 5),
      $text_color,
      $font,
      $string
    );


    $imm_info = array (
      0 => $original_width,
      1 => $original_height,
      'mime' => 'image/png'
    );

    if( empty($_GET['bb'])) {

      header('Content-Type: ' . $imm_info ['mime']);
      imagepng($im);
      exit();

    } else {
      $source = $im;
    }

  } else {
    $isGD = false;
    $imm_info=getimagesize($img_file);
    /*
    $imm_info:
    (
        [0] => 900
        [1] => 900
        [2] => 2
        [3] => width="900" height="900"
        [bits] => 8
        [channels] => 3
        [mime] => image/jpeg
    )
    */

    $pathinfo = pathinfo($img_file);
    /*
    (
        [dirname] => /Users/max/...
        [basename] => 5661.jpg
        [extension] => jpg
        [filename] => 5661
    )

    */

    $original_width = $imm_info[0];
    $original_height = $imm_info[1];
  }


  $crop = false;

  // dimensioni: calcolo in proporzione al bb dato
  if(!empty($_GET['bb']))
  {
  	$pos = strpos($_GET['bb'], 'x');

  	if($pos !== false) {

  		// 0 indica il calcolo automatico delle proporzioni
  		$bb_width = ($pos > 0)?substr($_GET['bb'], 0, $pos) : 0;
  		$bb_height = (($pos + 1) < strlen($_GET['bb']))? substr($_GET['bb'],$pos+1) : 0;

  		// per il crop è necessario specificare entrambe le dimensioni
  		$crop = isset($_GET['fd']) and $bb_width and $bb_height;

  		// le dimensioni richieste non possono superare quelle originali
  		if($bb_width > $original_width) $bb_width = $original_width;
  		if($bb_height > $original_height) $bb_height = $original_height;

  		if($crop) {

  			// nel ritaglio, inizialmente, l'immagine viene ridimensionata in modo che
  			// soddisfi il lato più lungo tra quelli richiesti

  			// primo tentativo: si assegna la base
  			$new_width  = $bb_width;
  			$new_height = round(( $new_width * $original_height ) / $original_width);

  			// secondo tentatico
  			if ($new_height < $bb_height) {
  				$new_height = $bb_height;
  				$new_width = round(( $new_height * $original_width ) / $original_height);
  			}

  //echo '<pre>'; print_r([ $bb_width , $bb_height, $original_width, $original_height, $new_width , $new_height]); echo '</pre><hr>'; exit;

  		} else {

  			// primo tentativo: si assume la nuova base = alla base del bb e si calcola l'altezza di conseguenza
  			$new_width  = $bb_width > 0 ? $bb_width : $original_width;
  			$new_height = round(( $new_width * $original_height ) / $original_width);

  			// se $bb_height > 0 e minore di $new_height si effettua un secondo tentativoi partendo dall'altezza
  			if($bb_height > 0 and $bb_height < $new_height ) {
  				$new_height = $bb_height > 0 ? $bb_height : $original_height;
  				$new_width = round(( $new_height * $original_width ) / $original_height);
  			}
  		}
  	} else {
  		die('bb non corretto');
  	}

  	//echo $original_width . ' - ' . $original_height. ' - ' . $new_width. ' - ' . $new_height;exit();

  	// Load
  	$resized = imagecreatetruecolor($new_width, $new_height);

    if(!$isGD) {
  	  $source = imagecreatefromjpeg($img_file);
    }

  	// Resize
  	//imagecopyresized($resized, $source, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
  	imagecopyresampled($resized, $source, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);


  	// crop
  	if($crop) {
  		$crop_array = [
  			'x'      => ($new_width - $bb_width ) / 2,
  			'y'      => ($new_height - $bb_height ) / 2,
  			'width'  => $bb_width,
  			'height' => $bb_height
  		];

  		$resized = imagecrop( $resized, $crop_array );
  	}

  	// ****************
  	// !stampa bb
    if(!isset($img_params['nobb'])) {
      $resized_width  = imagesx($resized);
      $resized_height = imagesy($resized);

      $text_color = imagecolorallocate($resized, 255, 255, 255);
      $bg_color = imagecolorallocate($resized, 120, 120, 120);

      $string = 'bb = '. $_GET['bb'];

      $bbpaddingX = 4;
      $bbpaddingY = 8;

      $text_sizes = calculate_text_sizes($string, $resized_width - ($bbpaddingX * 2), $resized_height);
      $bby = $text_sizes['y'] + $text_sizes['fontsize'] / 2; // $y del test bb


      imagefilledrectangle ( $resized,
        $text_sizes['x'] - $bbpaddingX, // int $x1
        $bby + $bbpaddingY, // int $y1
        $text_sizes['x'] + $text_sizes['w'] + ($bbpaddingX * 2), // int $x2
        $bby + $text_sizes['h'] - $bbpaddingY, // int $y2
        $bg_color
      );

      imagettftext($resized,
        $text_sizes['fontsize'],
        0,
        $text_sizes['x'] + $bbpaddingX,
        $bby,
        $text_color,
        $font,
        $string
      );
    }

    // ***************

  	// Output and free memory
  	switch ($imm_info ['mime']) {
  	    case 'image/jpeg':
  	    case 'image/pjpeg':
  	        header('Content-Type: ' . $imm_info ['mime']);
  	        imagejpeg($resized);
  	        break;
  	    case 'image/png':
  	    	header('Content-Type: ' . $imm_info ['mime']);
  	        imagepng($resized);
  	        break;
  	    case 'image/gif':
  	    	header('Content-Type: ' . $imm_info ['mime']);
  	        imagegif($resized);
  	        break;
  	    case 'image/wbmp':
  	    	header('Content-Type: ' . $imm_info ['mime']);
  	        imagewbmp($resized);
  	        break;

  	    default:
  			echo '<pre>Image type non supportato.';
  			print_r(gd_info());
  			echo '</pre><hr>';
  			exit();
  	}

  	imagedestroy($resized);

  } else {
  	header('Content-Type: ' . $imm_info ['mime']);
  	readfile($img_file);
  }
}
