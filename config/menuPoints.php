<?php
require_once BASE_DIR . "/config/constants.php";
$menuPoints = array(
  array(
    "title" => $constants["REGISTER"],
    "href" => "?page=register",
    "page" => "register",
    "roles" => array(
      $constants["ROLE_VISITOR"]
    )
  )
);