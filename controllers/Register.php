<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";

class Register {
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
    global $_POST, $constants;
    $data = $this->user->toArray();
    $data["pass2"] = "";
    $message = "";
    if (isset($_POST['register'])) {
      $res = $this->user->register($_POST);
      $data = $_POST;
      $message = $res[1];
    }
    $data["message"] = $message;
    $view = getView('register.html');
    $view = replaceValues($view, $data);
    return $view;
  }
}