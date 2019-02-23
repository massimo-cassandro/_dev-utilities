<?php

/*

	DA RISCRIVERE INTERAMENTE
	 - eliminare vecchia metodologia definizione elementi form twig
	 - unificare con twig_extensions e con quanto possibile in pagina_standard e test_config
	 - se possibile ottnere un unico file

*/



// EMULAZIONE FUNZIONI PER I FORM DI SYMFONY


/*
il file php di prova deve contenere un array equovalente a quello generato da symfony che contenga la mappatura di tutti campi:

    ****************************
    $form_array=array(
        // campi ...
    );

    foreach($form_array as $k => &$v) {
        if(is_array($v) and !isset($v['id'])) $v['id']=$k;
    }

    echo $twig->render('scheda.html.twig', array(
        'error'                => array(),
        'edit_form'            => $form_array
    }
    *****************************

    l'array deve contenere un sub_array per ogni campo mappato.
    Ogni campo è così strutturato:

        'chiave_campo' => array(
            'id' => '__id__', // id del campo
            'label'  =>
            'required' => true, // comportamento di default, definito dal db, può essere sovrascritto dagli attributi del campo
            'type' => 'text',
            'maxlength' => 255, // per i campi input, definito in produzione dal db
            'value'     => '', // per eventuali test
            'test_options' => array(
                1 => 'prova',
                2 => 'prova2'
            )

        )

    in cui:

    id                      => id assegnato al campo, se non presente viene utilizzata la chiave campo (grazie all'istruzione foreach da inserire dopo la definizione dell'array
    required: true | false  => il campo e' obbligatorio? (default: false). Può essere sovrascritto dai parametri inseriti direttmente nel form
    type: tipo di campo     => stringa del tipo di campo: text, time date ecc, textarea, select, checkbox o radio
    maxlength: lunghezza    => solo i campi input, attributo maxlength
    value                   => valore preregistrato da utilizzare per i test
    test_value              => alias di value
    test_options            => solo per i campi select, array degli options


esempio:

$form_array=array(
	  //'chiave_campo' => array('type' => 'text checkbox select date time textarea', 'maxlength' => 10, 'test_value' => '', 'test_options' => array()),

	 'cancellato' => array('type' => 'checkbox'),

	 'test_label' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),
	 //'chiave_campo' => array('type' => 'text'),

);


foreach($form_array as $k => &$v) {
    if(is_array($v) and !isset($v['id'])) $v['id']=$k;
}


*/

function RandomString($length) {
    $keys = array_merge(range(0,9), range('a', 'z'));

    $key = "";
    for($i=0; $i < $length; $i++) {
        $key .= $keys[mt_rand(0, count($keys) - 1)];
    }
    return $key;
}

function crea_attr_string($attrs) {
	$attr_string = '';
	foreach($attrs as $k => $v) {

	    if(!is_bool($v) ) {
	    	$attr_string .= " {$k}=\"{$v}\"";
		} else if ($v === true ) {
			$attr_string .= " {$k}";
		}
    }

    return $attr_string;
}

if(empty($framework)) $framework = 'bootstrap3';
if($framework == 'bootstrap' ) $framework = 'bootstrap3';

//!FORM
$function = new Twig_SimpleFunction('form_start', function ($form_array = array(), $opts = array()) {

	// {{ form_start(form, {attr: {id: 'form1', role: 'form', class: 'checkform'}}) }}
	if(!isset($opts['attr'])) $opts['attr'] = array();

	$attr_string = '';
	foreach ($opts['attr'] as $k => $v) {
		$attr_string .= " {$k}=\"{$v}\"";
	}
	if(!empty($opts['id'])) $attr_string .= " id=\"{$opts['id']}\"";
	if(!empty($opts['action'])) $attr_string .= " action=\"{$opts['action']}\"";

	echo "<form {$attr_string}>";

});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('form_errors', function () {
	// {{ form_errors(form) }}
	echo '';
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('form_rest', function () {
	echo '';
});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('form_end', function () {
	//{{ form_end(form) }}
	echo '</form>';
});
$twig->addFunction($function);


//!LABEL

function form_label($form_array = array(), $label = '', $attr = array()) {
	/*
		{{ form_label(form.xxx,
			'label',
			{'id': 'label',
			label_attr: {class: 'pippo'}
			}
		) }}
	*/

	global $framework;

	if(!isset($attr['label_attr'])) $attr['label_attr'] = array();

	//parametri test
	$test= !empty($opts['dev']) ?  $opts['dev'] : (!empty($opts['test_params'])? $opts['test_params'] : array());

	// parametro $test['usa_framework']
	// se presente e uguale a false, forza la restituzione del markup base
	if(!isset($test['usa_framework'])) $test['usa_framework'] = true;

	$label_attr = $attr['label_attr'];
	unset($attr['label_attr']);

	if(!isset($label_attr['class'])) $label_attr['class'] = '';
    if( strpos($label_attr['class'], 'control-label') === false and $framework == 'bootstrap3' and $test['usa_framework']) {
	    $label_attr['class'] .= ' control-label';
    }

	$attr = array_merge($attr, $label_attr);
	if(!empty($form_array['id'])) $attr['for'] = $form_array['id'];

    $attr_string = '';


    if(!$label and !empty($test['label'])) $label = $test['label'];
    if(!$label) $label = '_label_';

    foreach($attr as $k => $v) {
	    $attr_string .= " {$k}=\"{$v}\"";
    }

	echo "<label{$attr_string}>{$label}</label>";

}

$function = new Twig_SimpleFunction('form_label', function ($form_array = array(), $label = '', $attr = array()) {
	form_label($form_array, $label, $attr);
});
$twig->addFunction($function);

//!FORM WIDGET

function form_widget($form_array = array(), $opts = array()) {

	global $framework;
	$attrs = array();

	/*
		{{ form_widget(form.test1, {
			id: 'test1',
			attr: {'tabindex': ''}
		}) }}
	*/

	//parametri test
	$test = !empty($opts['dev']) ?  $opts['dev'] : (!empty($opts['test_params'])? $opts['test_params'] : array());
	if(isset($test['options'])) $test['test_options'] = $test['options'] ;

	// parametro $test['usa_framework']
	// se presente e uguale a false, forza la restituzione del markup base
	if(!isset($test['usa_framework'])) $test['usa_framework'] = true;

	// per compatibilità col sistema precedente
	if(isset($form_array['test_value'])) $test['test_value'] = $form_array['test_value'];
	if(isset($form_array['type'])) $test['type'] = $form_array['type'];

	if(empty($test['test_value'])) $test['test_value'] = '';

	// type
	$type = 'text';
	if(!empty( $opts['type'] )) {
		$type = strtolower( $opts['type'] );

	} else if(!empty( $test['type'])) {
		$type = strtolower( $test['type'] );

	}

	// id
	$attrs['id'] = '';
	if(!empty( $opts['id'] )) {
		$attrs['id'] = $opts['id'];
	}
	if( empty($attrs['id']) ) $attrs['id']= RandomString(6);

	// required
	if(!empty( $opts['required'] )) {
		$attrs['required'] = (bool)$opts['required'];
	}

	// maxlength
	if(!empty( $opts['max_length'] )) {
		$attrs['maxlength'] = $opts['max_length'];
	}

	// disabled
	if(!empty( $opts['disabled'] )) {
		$attrs['disabled'] = (bool)$opts['disabled'];

	} else if(!empty( $test['disabled'] )) {
		$attrs['disabled'] = (bool)$test['disabled'];
	}


	// class
	if(!isset( $opts['attr'] )) $opts['attr'] = array();
	if(!isset( $opts['attr']['class'] )) $opts['attr']['class'] = '';

	if( strpos($opts['attr']['class'], 'control-label') === false and
		$type!= 'checkbox' and
		$type!= 'radio' and
		$type!= 'serie_radio' and
		$type!= 'serie_radio_inline' and
		$type!= 'serie_checkbox' and
		$type!= 'serie_checkbox_inline' and
		$test['usa_framework'] and
		($framework == 'bootstrap3')) {
	    $opts['attr']['class'] .= ' form-control';
    }

	if($framework == 'bootstrap4' and $type == 'serie_radio' and $test['usa_framework'])  {
		$opts['attr']['class'] .= ' form-check-input';
	}

    // name (simulato)
    $attrs['name'] = !empty($test['name']) ? $test['name'] : "name_{$attrs['id']}";


    $attrs = array_merge( $attrs, $opts['attr']);

    $attr_string = crea_attr_string($attrs);

	$value ='';
	if(!empty($test['test_value'])) $value = $test['test_value']; // per compatibilità
	if(!empty($test['value'])) $value = $test['value'];

	// select
    if( $type == 'select') {
	    echo "<select {$attr_string}>\n" ;

/*
		// se test_options è un array, vuol dire che la variabile nasce come result di un select
        // e va linearizzato nella forma $k => $v
        // il primo elemento di test_options è l'array sorgente, il secondo è l'elemento da usare come chiave
        // il terzo come value
        if(is_array($test['test_options'])) {
	        $sorgente = $test['test_options'][0];
	        $chiave = $test['test_options'][1];
	        $valore = $test['test_options'][2];

	        $test['test_options'] = array();

	        foreach( $sorgente as $v ) {
		        $test['test_options'][$v[$chiave]] = $v[$valore];
	        }
        }
*/

        if(!empty( $test['test_options'] )) {

	        foreach($test['test_options'] as $k => $v) {
		        echo "<option value=\"{$k}\"" . ($value == $k? ' selected' : '' ) . ">{$v}</option>\n";
	        }

        }

        if(!empty( $test['options'] )) {

	        foreach( $test['options'] as $v) {
		        if( count($v) ) {
		        	echo "<option value=\"{$v[0]}\">{$v[1]}</option>\n";
		        } else {
			        echo '<option value=""></option>';
		        }
	        }

        }

        echo '</select>';

    // textarea
    } else if( $type == 'textarea' ) {

	    echo "<textarea {$attr_string}>{$value}</textarea>";

    // !checkbox
    } else if( $type == 'checkbox' ) {

	    echo '<input value="1" type="checkbox"' . $attr_string . ($value? ' checked' : '' ) . '>';

	// !radio
    } else if( $type == 'radio' ) {

	    echo '<input value="' . $value . '" type="radio"' . $attr_string . ($value? ' checked' : '' ) . '>';

	// !serie_radio
    } else if( $type == 'serie_radio' ) {

		echo '<div>';
		$cont = 0;
		if(!empty( $test['test_options'] )) {
			foreach($test['test_options'] as $v) {
				$this_attrs = $attrs;
				$this_attrs['id'] = "{$this_attrs['id']}_" . $cont++;
				$attr_string = crea_attr_string($this_attrs);

	            if( !$test['usa_framework']) {
		            echo '<input value="' . $v[0] . '" type="radio"' . $attr_string . ($value == $v[0]? ' checked' : '' ) . '>' .
		            	'<label for="' . $this_attrs['id'] . '">'. $v[1] . '</label>';

	            } else {
		            if($framework == 'bootstrap4') {
			            echo '<div class="form-check">' .
					      '<label class="form-check-label">' .
					      	'<input value="' . $v[0] . '" type="radio"' . $attr_string . ($value == $v[0]? ' checked' : '' ) . '>' .
					        $v[1] .
					      '</label>' .
					    '</div>';
		            }
		        }
			}
		}

		echo '</div>';

	// !serie_radio_inline
    } else if( $type == 'serie_radio_inline' ) {

		foreach($test['options'] as $k => $v) {

			if($framework == 'bootstrap3') {
				echo '<label class="radio-inline">' .
	                    '<input value="' . $k . '" type="radio"' . $attr_string . ($value == $k? ' checked' : '' ) . '>' .
	                    $v .
	                '</label>';

	        } else if ($framework == 'bootstrap4') {
		        echo '<label class="form-check-inline">' .
	                    '<input class="form-check-input" value="' . $k . '" type="radio"' . $attr_string . ($value == $k? ' checked' : '' ) . '>' .
	                    $v .
	                '</label>';
	        }
		}

	// !serie checkbox
    } else if( $type == 'serie_checkbox' ) {

		foreach($test['options'] as $k => $v) {

            if($framework == 'bootstrap3') {
	            echo '<div class="checkbox">' .
			      '<label>' .
			      	'<input value="' . $k . '" type="checkbox"' . $attr_string . ($value == $k? ' checked' : '' ) . '>' .
			        $v .
			      '</label>' .
			    '</div>';
            }
		}

	// !serie_checkbox_inline
    } else if( $type == 'serie_checkbox_inline' ) {

		foreach($test['options'] as $k => $v) {

			if($framework == 'bootstrap3') {
				echo '<label class="checkbox-inline">' .
	                    '<input value="' . $k . '" type="checkbox"' . $attr_string . ($value == $k? ' checked' : '' ) . '>' .
	                    $v .
	                '</label>';
	        }
		}

    } else {

	    echo "<input type=\"{$type}\" value=\"{$value}\"{$attr_string}>";

    }
}

$function = new Twig_SimpleFunction('form_widget', function ($form_array = array(), $opts = array()) {

	form_widget($form_array, $opts);
});
$twig->addFunction($function);


//!FORM ROW
$function = new Twig_SimpleFunction('form_row', function ($form_array = array(), $opts = array()) {
	/*
		{{ form_row(form.XXXX3, {
			'id': 'label3',
		    'label': 'label3',
			'help': 'help_block',
		    'attr': {'tabindex': '', class: 'pippo'}
		    })
		}}
	*/

	global $framework;

	$attr_string = array();
	//parametri test
	$test= !empty($opts['dev']) ?  $opts['dev'] : (!empty($opts['test_params'])? $opts['test_params'] : array());


	// per compatibilità col sistema precedente
	if(isset($form_array['test_value'])) $test['test_value'] = $form_array['test_value'];
	if(isset($form_array['type'])) $test['type'] = $form_array['type'];

	if(empty($test['test_value'])) $test['test_value'] = '';

	// parametro $test['usa_framework']
	// se presente e uguale a false, forza la restituzione del markup base
	if(!isset($test['usa_framework'])) $test['usa_framework'] = true;


	// type
	$type = 'text';
	if(!empty( $opts['type'] )) {
		$type = strtolower( $opts['type'] );

	} else if(!empty( $test['type'])) {
		$type = strtolower( $test['type'] );
	}

	$opts['type'] = $type;

	// id
	$id = '';
	if(!empty( $opts['id'] )) {
		$id = $opts['id'];
	} else if(!empty( $form_array['id'] )) {
		$id = $form_array['id'];
	}
	if( $id ) $id = RandomString(6);

	// help
	$help = '';
	if(!empty( $opts['help'] )) {
		$help = $opts['help'];
		unset($opts['help']);
	}

	// label
	$label = '';
	if(!empty( $opts['label'] )) {
		$label = $opts['label'];
	}
	if(!$label and !empty($test['label'])) $label = $test['label'];

	if($type == 'hidden') {

		form_widget($form_array, $opts);

	} else {

		if($framework == 'bootstrap3' and $test['usa_framework']) echo '<div class="form-group">' . "\n";

		if($type == 'checkbox' and $framework == 'bootstrap3' and $test['usa_framework']) {
			$opts['type'] = $type;
			echo '<div class="checkbox">' . "\n" .
                '<label class="control-label">' . "\n";
            form_widget($form_array, $opts);
            echo $label . "\n" .
                '</label>' . "\n" .
            '</div>';
		} else {

			$label_opts = array();
			$label_opts['label_attr'] = empty($opts['label_attr']) ? null : $opts['label_attr'];
			$label_opts['for'] = $id;

			form_label($form_array, $label, $label_opts);
			form_widget($form_array, $opts);
			if($help) {
				echo "<span class=\"help-block\">{$help}</span>\n";
			}
		}
		if($framework == 'bootstrap3' and $test['usa_framework']) echo '</div>';
	}
});
$twig->addFunction($function);

?>
