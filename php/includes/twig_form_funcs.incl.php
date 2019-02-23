<?php
// EMULAZIONE FUNZIONI PER I FORM DI SYMFONY
// con l'aggiunta di un parametro per l'inserimento dei dati di test

/*
  Documentazione symfony
  http://symfony.com/doc/current/reference/forms/twig_reference.html
  http://symfony.com/doc/current/reference/forms/types.html


  vengono restituiti i widget di symfony per la creazione degli elementi dei form:

  - form_start, form_end, form_errors, form_rest   → tag di apertura e chiusura dei form, token e ed errori relativi
  - form_widget                                    → elementi dei form
  - form_label                                     → label
  - form_row                                       → combinazione di widget e label

  Oltre a quelli standard, a `form_widget` e `form_row` può essere aggiunto il parametro `dev` per l'inserimento di dati di test
  che vengono ignorati in produzione.



  ELEMENTI DI `dev`:
  --------------------------
  - type      : forza il tipo di elemento (es. type: 'checkbox') che di default è `text`. È utilizzato solo se non è definito nel widget
  - value     : imposta il value dell'elemento (o l'option selected in caso di select)
  - options   : elenco di options. È un array di array in cui il primo valore di ogni coppia corrisponde al value
                e il secondo al testo. Inserire un array vuoto per avere un option vuoto (es: [[], [1, 'xxx'], [2, 'yyy']])
  - label     : label dell'elemento. È utilizzato solo se non è definito nel widget

  - attr, label_attr : aggiunge parametri `attr` e `label_attr` a fini di test


  Esempio:

  {{ form_row(form.XXXX, {
    id: 'XXXX',
    label: 'XXXXX',
    attr: {tabindex: ''},
    dev: {
      type: 'text',
      label: 'xxx',
      value: 'xx',
      options: [[], [1, 'xxx'], [2, 'yyy']],
      attr: {},
      label_attr: {}
    }
  }) }}

  Eventuali attributi checked, disabled, ecc. possono essere impostati in dev attraverso il parametro `attr`
  (esempio: `dev:{ attr:{ checked: true }}` )


  Viene anche gestito il parametro `help` di `form_row` che costruisce il tag per il testo
  di aiuto di un elemento form (in produzione deve essere presente un override per i widget)

*/

//*******************************************
// !UTILITÀ
//*******************************************


// il framework front end va dichiarato nel file principale dell'ambiente di sviluppo
if(empty($framework)) $framework = null;

//! > crea_attr_string
function crea_attr_string($array_attrs) {
  // costruisce una stringa di attributi HTML per i gli elementi form
  $attr_string = [];
  foreach($array_attrs as $k => $v) {

      if(!is_bool($v) ) {
        $attr_string[] = $k . '="' . htmlentities($v, ENT_COMPAT) .'"';
    } else if ($v === true ) {
      $attr_string[] = "{$k}";
    }
  }

    return implode(' ', $attr_string);
}

function RandomString($length) {
    $keys = array_merge(range(0,9), range('a', 'z'));

    $key = "";
    for($i=0; $i < $length; $i++) {
        $key .= $keys[mt_rand(0, count($keys) - 1)];
    }
    return $key;
}

function creaRadioCheckbox($id, $class = '', $value='', $params) {

  $element_type = in_array($params['type'], array('serie_radio', 'serie_radio_inline')) ? 'radio' : 'checkbox';
  if($class) {
    if($params['attr']['class']) {
      $params['attr']['class'].= " {$class}";
    } else {
      $params['attr']['class'] = $class;
    }
  }


  $item_attr_string = crea_attr_string( array_merge($params['attr'], array('id' => $id )) );

  return '<input value="' . $value . '" type="' . $element_type .'" ' .
      $item_attr_string .
      ($params['value'] == $value? ' checked' : '' ) .
      '>';
}


//! > elaboraParametriWidget
// setup di base per i parametri del widget
function elaboraParametriWidget( $form_array = array(), $opts = array() ) {
  global $framework;


  if($form_array == null ) $form_array = array();

  // dev_parsed è impostato dopo l'elaborazione per evitare che sia eseguita di nuovo
  if(!isset($opts['dev_parsed'])) {
    // array dei parametri elaborati. Unisce quelli di produzione con i parametri dev
    $params = array();

    $opts = array_merge_recursive($opts, $form_array);

//echo '<pre><div>form_array</div>'; print_r($form_array); echo '</pre><hr>';
//echo '<p>gettype: ' . gettype($form_array) . '</p>';
//echo '<pre><div>OPTS</div>'; print_r($opts); echo '</pre><hr>';


    //! >> default
    $params['attr'] = !empty($opts['attr']) ?  $opts['attr'] : array();
    $params['label_attr'] = !empty($opts['label_attr']) ?  $opts['label_attr'] : array();
    $params['dev'] = !empty($opts['dev']) ?  $opts['dev'] : array();
    if(!isset($params['dev']['attr'])) $params['dev']['attr'] = array();
    if(!isset($params['dev']['label_attr'])) $params['dev']['label_attr'] = array();

    $params['help'] = isset($opts['help']) ? $opts['help'] : null;

    if( isset($opts['setlabel']) ) $params['setlabel'] = $opts['setlabel'];

    //! >> classi label
    if(!isset($params['label_attr']['class'])) $params['label_attr']['class'] = '';


    //! >> testo label
    $params['label'] = '__label__';
    if( !empty($opts['label']) ) {
      $params['label'] = $opts['label'];
    } else if( !empty($form_array['label']) ) {
      $params['label'] = $form_array['label'];
    } else if( !empty($params['label_attr']['label']) ) {
      $params['label'] = $params['label_attr']['label'];
    } else if ( !empty($params['dev']['label']) ) {
      $params['label'] = $params['dev']['label'];
    }
    $params['label_attr']['label'] = $params['label'];

    //! >> id e for
    if(!empty($opts['id'])) {
      $params['id'] = $opts['id'];
    } else {
      $params['id'] = RandomString(6);
    }
    $params['attr']['id'] = $params['id'];
    if(empty($params['label_attr']['for'])) $params['label_attr']['for'] = $params['id'];

    //! >> type
    $params['type'] = strtolower(
      !empty($form_array['type'])? $form_array['type'] : (
        !empty($opts['type'])? $opts['type'] : (
          !empty($params['dev']['type']) ? $params['dev']['type'] : 'text'
        )
      )
    );

    //! >> classi widget
    if(!isset( $params['attr']['class'] )) $params['attr']['class'] = '';

    if ( in_array($params['type'], array('checkbox', 'radio')) ) {
      $params['attr']['class'] .= ' form-check-input';

    } else {

      $params['attr']['class'] .= ' form-control';
    }

    if($params['type'] == 'select') {
      $params['attr']['class'] .= " custom-select";
    }

    // name (simulato)
    $params['attr']['name'] = !empty($params['dev']['attr']['name']) ? $params['dev']['attr']['name'] : "name_{$params['id']}";

    // value (non viene inserito in attr perché gestito diversamente dai tag)
    $params['value'] = !empty($params['dev']['value'])? $params['dev']['value'] : '';


    //! >> passaggio dei parametri attr e label_attr di test al livello principale
    foreach ($params['dev']['attr'] as $k => $v ) {
      if(!isset( $params['attr'][$k] )) $params['attr'][$k] = $v;
    }
    foreach ($params['dev']['label_attr'] as $k => $v ) {
      if(!isset( $params['label_attr'][$k] )) $params['label_attr'][$k] = $v;
    }

    //! >> required e disabled
    $params['required'] = strtolower( !empty($opts['required'])? $opts['required'] : ( !empty($params['dev']['required']) ? $params['dev']['required'] : false ) );
    if(!empty( $params['required'] )) $params['attr']['required'] = (bool)$params['required'];
    $params['disabled'] = strtolower( !empty($opts['disabled'])? $opts['disabled'] : ( !empty($params['dev']['disabled']) ? $params['dev']['disabled'] : false ) );
    if(!empty( $params['disabled'] )) $params['attr']['disabled'] = (bool)$params['disabled'];


    //! >> stringhe attributi
    $params['label_attr_string'] = crea_attr_string($params['label_attr']);
    $params['attr_string'] = crea_attr_string($params['attr']);


    //echo '<pre>'; print_r($params); echo '</pre><hr>';

    // evita doppie elaborazioni
    $params['dev_parsed'] = true;
//echo '<pre>'; print_r($params); echo '</pre><hr>';

    return $params;


  } else {
    return $opts;
  }
}

//*******************************************
// !TAG FORM
//*******************************************

$function = new Twig_SimpleFunction('form_start', function ($form_array = array(), $opts = array()) {

  // {{ form_start(form, {attr: {id: 'form1', role: 'form', class: 'checkform'}}) }}
  if(!isset($opts['attr'])) $opts['attr'] = array();

  $attr_string = crea_attr_string($opts['attr']);

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




//*******************************************
// !FORM_LABEL
//*******************************************
function form_label($form_array = array(), $label = '', $label_vars = array()) {
  /*

  La stringa label può essere inserita come secondo elemento o dichiarata esplicitamente in label_attr

  {{ form_label(form.name, 'Your Name', {'label_attr': {'class': 'foo'}}) }}

  {{ form_label(form.name, null, {
      'label': 'Your name',
      'label_attr': {'class': 'foo'}
  }) }}
  */

  if( $label ) {
    $label_vars['label'] = $label;
  }

   $params = elaboraParametriWidget( $form_array, $label_vars );

  if(empty($params['tags_pieces'])) $params['tags_pieces'] = false;

  if($params['tags_pieces']) {
    return array("<label {$params['label_attr_string']}>", $params['label'], '</label>');

  } else {
    echo "<label {$params['label_attr_string']}>{$params['label']}</label>";
  }


}
$function = new Twig_SimpleFunction('form_label', function ($form_array = array(), $label = '', $label_vars = array()) {
  form_label($form_array, $label, $label_vars);
});
$twig->addFunction($function);

//*******************************************
// !FORM_WIDGET
//*******************************************

function form_widget($form_array = array(), $widget_vars = array()) {
  /*
    {{ form_widget(form.XXXX, {
      id: 'XXXXX',
      required: true,
      attr: {tabindex: ''},
      test: { type: 'text' }
    }) }}
  */

  global $framework;
  $params = elaboraParametriWidget( $form_array, $widget_vars );

  // ! > select
  if( $params['type'] == 'select') {
    echo "<select {$params['attr_string']}>\n" ;

    if(!empty( $params['dev']['options'] )) {

      foreach( $params['dev']['options'] as $v) {
        if( count($v) ) {
          echo "<option value=\"{$v[0]}\"" . ($params['value'] == $v[0]? ' selected' : '' ) . ">{$v[1]}</option>\n";
        } else {
          echo '<option value=""></option>';
        }
      }
    }

    echo '</select>';

  // ! > textarea
  } else if( $params['type'] == 'textarea' ) {

    echo "<textarea {$params['attr_string']}>{$params['value']}</textarea>";

  // ! > checkbox
  } else if( $params['type'] == 'checkbox' ) {

    echo '<input value="' . ($params['value'] ? $params['value'] : '1') . "\" type=\"checkbox\" {$params['attr_string']}>";

  // ! > radio
  } else if( $params['type'] == 'radio' ) {

    echo "<input value=\"{$params['value']}\" type=\"radio\" {$params['attr_string']}>";

  //! > altri tag
  } else {

    echo "<input value=\"{$params['value']}\" type=\"{$params['type']}\" {$params['attr_string']}>";

  }
} // end form_widget
$function = new Twig_SimpleFunction('form_widget', function ($form_array = array(), $widget_vars = array()) {

  form_widget($form_array, $widget_vars);
});
$twig->addFunction($function);


//!FORM ROW
$function = new Twig_SimpleFunction('form_row', function ($form_array = array(), $row_vars = array()) {
  /*
    {{ form_row(form.XXXX3, {
      'id': 'label3',
      'label': 'label3',
      'help': 'help_block',
      'attr': {'tabindex': '', class: 'pippo'}
      })
    }}
  */

  $params = elaboraParametriWidget( $form_array, $row_vars );

  if($params['type']== 'hidden') {
    form_widget($form_array, $row_vars);

  } else if ( $params['type']== 'checkbox' ) {



    if(isset($params['setlabel']) and $params['setlabel'] == 'single') {

      echo '<div class="form-group">' .
        '<div class="form-check form-check-single">';
      form_label($form_array, null, $params);
      form_widget($form_array, $params);
      echo '</div>';
      if($params['help']) {
        echo '<div class="form-help-text">' . $params['help'] . '</div>';
      }
      echo '</div>';

    } else {

      $this_label = form_label($form_array, null, array_merge($params, array('tags_pieces' => true)));
      echo '<div class="form-group">' .
        '<div class="form-check">' .
          $this_label[0];
      form_widget($form_array, $params);
      echo $this_label[1] .
          $this_label[2] . // end label
        '</div>';
      if($params['help']) {
        echo '<div class="form-help-text">' . $params['help'] . '</div>';
      }
      echo '</div>';
    }

  } else {
    echo '<div class="form-group">';
    form_label($form_array, null, $params);
    form_widget($form_array, $params);
    if($params['help']) {
      echo '<div class="form-help-text">' . $params['help'] . '</div>';
    }
    //form_widget($form_array, $row_vars);
    echo '</div>';
  }

});
$twig->addFunction($function);

?>
