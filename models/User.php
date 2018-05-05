<?php

require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/models/Roles.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

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
  private $role = "";


  private $roles;
  private $getUserByEmailSQL = "SELECT * FROM felhasznalo WHERE email = '{{email}}'";

  /**
   * User constructor.
   * @param $user
   */
  public function __construct($user) {
    parent::__construct();
    global $_SESSION, $constants;
    $this->roles = new Roles();
    $this->role = $this->roles->getRoleId($constants["ROLE_USER"]);
    if ($this->isLoggedIn()) {
      $this->setUser($_SESSION["login"]);
    }
    $this->setUser($user);
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
   * Visszaadja, hogy lektor-e a felhasználó
   * @return bool
   */
  public function isLektor() {
    global $_SESSION, $constants;
    return ($this->isLoggedIn() && $_SESSION["login"]["role"] === $this->roles->getRoleId($constants["ROLE_LEKTOR"]));
  }

  /**
   * Visszaadja, hogy admin-e a felhasználó
   * @return bool
   */
  public function isAdmin() {
    global $_SESSION, $constants;
    return ($this->isLoggedIn() && $_SESSION["login"]["role"] === $this->roles->getRoleId($constants["ROLE_ADMIN"]));
  }

  /**
   * Visszaadja a user jogosultságait
   * @return array
   */
  public function getRoles() {
    global $constants;
    $roles = array($constants["ROLE_VISITOR"]);
    if ($this->isLoggedIn()) {
      array_push($roles, $constants["ROLE_USER"]);
    }
    if ($this->isLektor()) {
      array_push($roles, $constants["ROLE_LEKTOR"]);
    }
    if ($this->isAdmin()) {
      array_push($roles, $constants["ROLE_ADMIN"]);
    }
    return $roles;
  }

  /**
   * Felhasználó regisztráció
   * @param $user
   * @return array
   */
  public function register($user) {
    global $constants;
    if (!$this->validate($user)) {
      return array(false, $constants["USER_NOT_VALID"]);
    }
    if (!$this->validate_password($user)) {
      return array(false, $constants["USER_NOT_VALID_PASSWORD"]);
    }
    if ($this->isUserExists($user)) {
      return array(false, $constants["USER_ALREADY_EXISTS"]);
    }
    $this->setUser($user);
    $this->pass = md5($this->pass);
    if (!$this->createNewUser()) {
      return array(false, $constants["USER_REGISTER_FAILED"]);
    }
    return array(true, $constants["USER_REGISTER_SUCCESS"]);
  }

  /**
   * Felhasználó adatok validálása
   * @param $user
   * @return bool
   */
  public function validate($user) {
    if (!$user["name"]) return false;
    if (!$user["nickname"]) return false;
    if (!$user["email"]) return false;
    return true;
  }

  /**
   * Jelszó egyezés validálása
   * @param $user
   * @return bool
   */
  public function validate_password($user) {
    if (!$user["pass"]) return false;
    if (!$user["pass2"]) return false;
    return $user["pass"] === $user["pass2"];
  }

  /**
   * Felhasználó létezés ellenőrzése
   * @param $user
   * @return bool
   */
  public function isUserExists($user) {
    $sql = replaceValues($this->getUserByEmailSQL, $user);
    $foundUsers = $this->query($sql)->getFetchedResult();
    return count($foundUsers) > 0;
  }

  /**
   * Új felhasználó létrehozása
   * @return mixed
   */
  public function createNewUser() {
    $sql = "INSERT INTO felhasznalo (name, nickname, email, pass, role) VALUES " .
      "('{{name}}', '{{nickname}}', '{{email}}', '{{pass}}', {{role}})";
    $sql = replaceValues($sql, $this->toArray());
    return $this->query($sql)->getResult();
  }

}