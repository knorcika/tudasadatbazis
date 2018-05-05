<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Category extends DB {
  private $categories = array();

  private $selectCategoriesSQL = "SELECT * FROM kategoria WHERE nyelv = {{lang}}";

  /**
   * Category constructor.
   * @param $lang
   */
  public function __construct($lang) {
    parent::__construct();
    $categories = $this->query(replaceValues($this->selectCategoriesSQL, array("lang" => $lang)))->getFetchedResult();
    foreach ($categories as $row) {
      $this->categories[$row["id"]] = $row["name"];
    }
  }

  /**
   * Visszaadja a kategóriákat
   * @return array
   */
  public function getCategories() {
    return $this->categories;
  }

  /**
   * Visszaadja a kategória nevét
   * @param $id
   * @return mixed
   */
  public function getCategoryById($id) {
    return $this->categories[$id];
  }
}