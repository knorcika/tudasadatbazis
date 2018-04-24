<?php

require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";

class User extends DB {
  private $id = "";
  private $name = "";
  private $nickname = "";
  private $email = "";
  private $pass = "";
  private $pass2 = "";
  private $tud_fokozat = "";
  private $intezet = "";
  private $szakterulet = "";
  private $role = "user";

  /**
   * User constructor.
   * @param $user
   */
  public function __construct($user) {
    global $_SESSION, $constants;
    if ($this->isLoggedIn()) {
      foreach ($_SESSION["login"] as $key => $val) {
        if (property_exists($this, $key)) {
          $this->$key = $val;
        }
      }
    }
    $this->setUser($user);
    parent::__construct();
  }

  /**
   * Visszaadja, hogy be van-e jelentkezve a felhasználó
   * @return bool
   */
  public function isLoggedIn() {
    global $_SESSION;
    return (isset($_SESSION["login"]) && $_SESSION["login"]["logged_in"]);
  }

  /**
   * Beállítja a felhasználó attribútumait tömb alapján
   * @param $user
   */
  public function setUser($user) {
    foreach ($user as $key => $val) {
      if (property_exists($this, $key)) {
        $this->$key = $val;
      }
    }
  }

  /**
   * Átalakítja ezt a felhasználót tömbbé
   * @return array
   */
  public function toArray() {
    return array(
      "id" => $this->id,
      "name" => $this->name,
      "nickname" => $this->nickname,
      "email" => $this->email,
      "pass" => $this->pass,
      "tud_fokozat" => $this->tud_fokozat,
      "intezet" => $this->intezet,
      "szakterulet" => $this->szakterulet,
      "role" => $this->role,
    );
  }

  /**
   * Kijelentkezés
   * @return bool
   */
  public function logout() {
    global $_SESSION;
    unset($_SESSION["login"]);
    return true;
  }

  /**
   * Visszaadja, hogy admin-e a felhasználó
   * @return bool
   */
  public function isAdmin() {
    global $_SESSION;
    return ($this->isLoggedIn() && $_SESSION["login"]["role"] === "admin");
  }

}