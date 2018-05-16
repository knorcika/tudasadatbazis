<?php
/**
 * Behelyettesíti a megfelelő adattagokat
 * @param $text
 * @param $data
 * @return mixed
 */
function replaceValues($text, $data) {
  foreach ($data as $key => $val) {
    if (is_string($val) || is_numeric($val)) {
      $text = str_replace("{{{$key}}}", $val, $text);
    }
  }
  return $text;
}