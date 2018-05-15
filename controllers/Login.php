<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";

class Login {
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
    $message = "";
    if (isset($_POST['login'])) {
      $res = $this->user->login($_POST);
      if ($res[0]) {
        return header("Location: index.php?page=&lang=" . $this->lang);
      }
      $data = $_POST;
      $message = $res[1];
    }
    $data["message"] = $message;
    $view = getView('login.html');
    $view = replaceValues($view, $data);
    return $view;
  }
}