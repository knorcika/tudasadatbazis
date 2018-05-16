<?php
require_once BASE_DIR . "/config/constants.php";
$menuPoints = array(
  array(
    "title" => $constants["BE_LEKTOR"],
    "href" => "?page=be_lektor",
    "page" => "be_lektor",
    "roles" => array(
      $constants["ROLE_USER"]
    )
  ),
  array(
    "title" => $constants["REGISTER"],
    "href" => "?page=register",
    "page" => "register",
    "roles" => array(
      $constants["ROLE_VISITOR"]
    )
  ),
  array(
    "title" => $constants["LOGIN"],
    "href" => "?page=login",
    "page" => "login",
    "roles" => array(
      $constants["ROLE_VISITOR"]
    )
  ),
  array(
    "title" => $constants["LOGOUT"],
    "href" => "?page=logout",
    "page" => "logout",
    "roles" => array(
      $constants["ROLE_USER"],
      $constants["ROLE_LEKTOR"],
      $constants["ROLE_ADMIN"]
    )
  )
);