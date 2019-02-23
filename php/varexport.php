<?php
function varexport($expression, $return=FALSE) {
  $export = var_export($expression, TRUE);
  $export = preg_replace("/^([ ]*)(.*)/m", '$1$1$2', $export);
  $array = preg_split("/\r\n|\n|\r/", $export);
  $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [NULL, ')$1', ' => array ('], $array);
  $export = join(PHP_EOL, array_filter(["array ("] + $array));
  if ((bool)$return) return $export; else echo htmlspecialchars($export);
}
