<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";
require_once BASE_DIR . "/models/Lang.php";

class AdminLektorJelentkezes {
  private $page = "";
  private $user;
  private $lang;

  /**
   * AdminLektorJelentkezes constructor.
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
      //itt a felhasználóid amit updatelni kell adminná
      $id = $_POST['submit'];
    }

    $jelentkezok = $this->getJelentkezok();
    $data = array(
      "jelentkezok" => $jelentkezok,
      "message" => $message
    );
    $view = getView('admin/lektorJelentkezesek.html');
    $view = replaceValues($view, $data);
    return $view;
  }

  public function getJelentkezok() {
    $data = $this->user->getSignedLectors();
    $jelentkezok = "";
    $view = getView('admin/jelentkezok.html');
    $langs = new Lang();
    $languages = $langs->getLanguages();
    foreach ($data as $row) {
      $row["lektornyelvek"] = "<ul>";
      foreach ($row["nyelvek"] as $key => $val) {
        $row["lektornyelvek"] .= "<li>" . $languages[$key] . " - " . $val . "</li>" . PHP_EOL;
      }
      $row["lektornyelvek"] .= "</ul>" . PHP_EOL;
      $jelentkezo = replaceValues($view, $row);
      $jelentkezok .= $jelentkezo . PHP_EOL;
    }
    return $jelentkezok;
  }
}