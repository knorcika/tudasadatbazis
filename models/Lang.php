<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Lang extends DB {
  private $languages = array();

  private $selectLanguagesSQL = "SELECT * FROM nyelvek";

  /**
   * Lang constructor.
   */
  public function __construct() {
    parent::__construct();
    $languages = $this->query($this->selectLanguagesSQL)->getFetchedResult();
    foreach ($languages as $row) {
      $this->languages[$row["id"]] = $row["name"];
    }
  }

  /**
   * Visszaadja az összes nyelvet.
   * @return array
   */
  public function getLanguages() {
    return $this->languages;
  }

  /**
   * Visszaadja az alapértelmezett nyelvet
   * @return int
   */
  public function getDefaultLanguage() {
    global $constants;
    foreach ($this->languages as $key => $val) {
      if ($val === $constants["DEFAULT_LANG"]) {
        break;
        return $key;
      }
    }
    return 1;
  }
}