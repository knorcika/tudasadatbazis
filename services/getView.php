<?php
/**
 * Betölti a megfelelő viewt
 * @param $path
 * @return bool|string
 */
function getView($path) {
  return file_get_contents(BASE_DIR . "/public/views/" . $path);
}