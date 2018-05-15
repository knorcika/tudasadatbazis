<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Topic extends DB {
  private $topics = array();
  private $topicsByCategory = array();

  private $selectTopicsSQL = "SELECT * FROM temakor";

  /**
   * Category constructor.
   */
  public function __construct() {
    parent::__construct();
    $topics = $this->query($this->selectTopicsSQL)->getFetchedResult();
    foreach ($topics as $row) {
      $this->topics[$row["id"]] = $row["name"];
      if (!isset($this->topicsByCategory[$row["kategoria"]])) {
        $this->topicsByCategory[$row["kategoria"]] = array();
      }
      array_push($this->topicsByCategory[$row["kategoria"]], $row["id"]);
    }
  }

  /**
   * Visszaadja a témaköröket
   * @return array
   */
  public function getTopics() {
    return $this->topics;
  }

  /**
   * Visszaadja a témakör nevét
   * @param $id
   * @return mixed
   */
  public function getTopicById($id) {
    return $this->topics[$id];
  }

  /**
   * Visszaadja a témakör id-kat kategória id alapján
   * @param $catId
   * @return mixed
   */
  public function getTopicsByCategory($catId) {
    return $this->topicsByCategory[$catId];
  }
}