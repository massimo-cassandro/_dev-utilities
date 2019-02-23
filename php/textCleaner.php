<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*

ripulisce i campi text delle tabelle indicate eliminando le porcherie da copia & incolla


*/

function textCleaner($str) {
	// ripulisce una stringa di testo html dal markup indesiderato proveniente da copia&incolla da word

	if($str) {
		$tags_to_remove = array('span', 'script', 'font', 'big', 'small', 'strike', 'u', 'tt', 'code',
			'form', 'input', 'select', 'option', 'textarea', 'button', 'label', 'fieldset', 'legend', 'object',
			'iframe', 'embed', 'center', 'div', 'section', 'article', 'blockquote', 'o:p'
		);
		$attrs_to_remove = array('style', 'class', 'align', 'id', 'valign', 'width', 'height', 'hspace', 'vspace',
			'onclick', 'name', 'cellspacing', 'cellpadding', 'bgcolor'
		);

		foreach($tags_to_remove as $tag) {
			$str = preg_replace('/<\/?' . $tag . '(.*?)>/i', '', $str);
		}

		foreach($attrs_to_remove as $attr) {
			$str = preg_replace('/ ' . $attr . '="(.*?)"/i', '', $str);
			$str = preg_replace('/ ' . $attr . "='(.*?)'/i", '', $str);
		}
		
		$str = preg_replace('/\\r\\n/', chr(10), $str);
		$str = preg_replace('/\\r/', '', $str);
		
		// attributi data
		$str = preg_replace('/ data-(.*?)="(.*?)"/i', '', $str);

		$str = str_replace(' class="MsoNormal"', '', $str);
		$str = preg_replace('/(&nbsp;)+/i', ' ', $str);
		$str = preg_replace('/<p> *<\/p>/i', '', $str);
		$str = preg_replace('/<h2> *<\/h2>/i', '', $str);
		$str = preg_replace('/<h3> *<\/h3>/i', '', $str);
	}

	return $str;
}



