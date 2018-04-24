<?php
require_once BASE_DIR . "/config/constants.php";
$menuPoints = array(
  array(
    "title" => "Test",
    "href" => "?page=",
    "page" => "",
    "roles" => array(
      $constants["ROLE_VISITOR"],
      $constants["ROLE_USER"],
      $constants["ROLE_LEKTOR"],
      $constants["ROLE_ADMIN"]
    )
  )
);