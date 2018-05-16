<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";
require_once BASE_DIR . "/models/Lang.php";

class BeLektor {
  private $page = "";
  private $user;
  private $lang;

  /**
   * BeLektor constructor.
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
    $message = "";
    if (isset($_POST['submit'])) {
      $res = $this->user->insertLektor($_POST);
      $message = $res[1];
    }
    $data = $this->user->toArray();
    $data["lektornyelvek"] = $this->getLangs();
    $view = getView('lektor/belektor.html');
    $data["message"] = $message;
    $view = replaceValues($view, $data);
    return $view;
  }

  public function getLangs() {
    $lektorNyelvek = "";
    $view = getView('lektor/lektorNyelvek.html');
    $langs = new Lang();
    foreach ($langs->getLanguages() as $id => $nyelv) {
      $lektorNyelv = replaceValues($view, array("id" => $id, "nyelv" => $nyelv));
      $lektorNyelvek .= $lektorNyelv . PHP_EOL;
    }
    return $lektorNyelvek;
  }
}