<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";

class Logout {
  private $page = "";
  private $user;
  private $lang;

  /**
   * Menu constructor.
   * @param $page
   * @param $user
   */
  public function __construct($page, $user, $lang) {
    $this->page = $page;
    $this->user = $user;
    $this->lang = $lang;
  }

  /**
   * Oldal törzs összerakása
   * @return mixed
   */
  public function getBody() {
    $this->user->logout();
    return header("Location: index.php?page=");
  }
}