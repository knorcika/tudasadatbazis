<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";

class Roles extends DB {
  private $rolesByName = array();
  private $rolesById = array();
  private $getRolesSQL = "SELECT * FROM roles";

  /**
   * Roles constructor.
   */
  public function __construct() {
    parent::__construct();
    $roles = $this->query($this->getRolesSQL)->getFetchedResult();
    foreach ($roles as $row) {
      $this->rolesByName[$row["name"]] = $row["id"];
      $this->rolesById[$row["id"]] = $row["name"];
    }
  }

  /**
   * Visszaadja a role id-t név alapján
   * @param $name
   * @return mixed
   */
  public function getRoleId($name) {
    return $this->rolesByName[$name];
  }

  /**
   * Visszaadja a role nevét id alapján
   * @param $id
   * @return mixed
   */
  public function getRoleName($id) {
    return $this->rolesById[$id];
  }
}