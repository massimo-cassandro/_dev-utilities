<?php
$array_esito=array(
	'file_name'=> 'nome_file_originale molto molto molto molto luuuuuungo.jpg',
	'tmp_file' => 'file_temporaneo',
	'size'=> 123456789,
	'esito' => true,
	'mes' => null
);

echo json_encode($array_esito);
?>