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
  private $lektorid = "";
  private $tud_fokozat = "";
  private $intezet = "";
  private $szakterulet = "";
  private $nyelvek = array();
  private $role = "";


  private $roles;
  private $getUserByEmailSQL = "SELECT * FROM felhasznalo WHERE email = '{{email}}'";
  private $insertUserSQL = "INSERT INTO felhasznalo (name, nickname, email, pass, role) VALUES " .
  "('{{name}}', '{{nickname}}', '{{email}}', '{{pass}}', {{role}})";
  private $getLektorData = "SELECT lektor.tud_fokozat, lektor.intezet, lektor.szakterulet, " .
  "lektor.id as lektorid, lektornyelv.nyelv, lektornyelv.szint " .
  "FROM lektor INNER JOIN lektornyelv ON lektor.id = lektornyelv.lektor WHERE lektor.felhasznalo = {{id}}";

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
   * Kijelentkezés
   * @return bool
   */
  public function logout() {
    global $_SESSION;
    unset($_SESSION["login"]);
    return true;
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
   * Visszaadja a user legmagasabb jogosultságát
   * @return string
   */
  public function getRole() {
    global $constants;
    if ($this->isAdmin()) {
      return $constants["ROLE_ADMIN"];
    }
    if ($this->isLektor()) {
      return $constants["ROLE_LEKTOR"];
    }
    if ($this->isLoggedIn()) {
      return $constants["ROLE_USER"];
    }
    return $constants["ROLE_VISITOR"];
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
   * Felhasználó regisztráció
   * @param $user
   * @return array
   */
  public function register($user) {
    global $constants;
    if (!$this->validate($user)) {
      return array(false, $constants["USER_NOT_VALID"]);
    }
    if (!$this->validatePassword($user)) {
      return array(false, $constants["USER_NOT_VALID_PASSWORD"]);
    }
    if (count($this->getUserByEmail($user)) > 0) {
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
  public function validatePassword($user) {
    if (!$user["pass"]) return false;
    if (!$user["pass2"]) return false;
    return $user["pass"] === $user["pass2"];
  }

  /**
   * Visszaadja a felhasználót e-mail cím alapján
   * @param $user
   * @return array
   */
  public function getUserByEmail($user) {
    $sql = replaceValues($this->getUserByEmailSQL, $user);
    return $this->query($sql)->getFetchedResult();
  }

  /**
   * Új felhasználó létrehozása
   * @return mixed
   */
  public function createNewUser() {
    $sql = replaceValues($this->insertUserSQL, $this->toArray());
    return $this->query($sql)->getResult();
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
      "nyelvek" => $this->nyelvek,
      "role" => $this->role,
    );
  }

  /**
   * Felhasználó bejelentkeztetése
   * @param $user
   * @return array
   */
  public function login($user) {
    global $_SESSION, $constants;
    if (!$this->validateLoginFields($user)) {
      return array(false, $constants["USER_LOGIN_EMPTY"]);
    }
    $foundUser = $this->getUserByEmail($user);
    if (count($foundUser) !== 1) {
      return array(false, $constants["USER_LOGIN_NOT_FOUND"]);
    }
    $foundUser = $foundUser[0];
    if (md5($user["pass"]) !== $foundUser["pass"]) {
      return array(false, $constants["USER_LOGIN_WRONG_PASSWORD"]);
    }
    $this->setUser($foundUser);

    if ($foundUser["role"] === $this->roles->getRoleId($constants["ROLE_LEKTOR"])) {
      $data = $this->query(replaceValues($this->getLektorData, $foundUser))->getFetchedResult();
      if (count($data) > 0) {
        $this->setUser($data[0]);
        foreach ($data as $row) {
          $this->nyelvek[$row["nyelv"]] = $row["szint"];
        }
      }
    }

    $user = $this->toArray();
    unset($user["pass"]);
    $user["logged_in"] = true;
    $_SESSION['login'] = $user;
    return array(true, $constants["USER_LOGIN_SUCCESS"]);
  }

  /**
   * A login form validálása
   * @param $user
   * @return bool
   */
  public function validateLoginFields($user) {
    if (!$user["email"]) return false;
    if (!$user["pass"]) return false;
    return true;
  }
}