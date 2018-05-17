<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Keyword extends DB {
  private $keywords = array();

  private $selectKeywordsSQL = "SELECT * FROM kulcsszo WHERE name = '{{name}}' AND nyelv = {{nyelv}}";
  private $insertKeywordsSQL = "INSERT INTO kulcsszo (name, nyelv) VALUES ('{{name}}', {{nyelv}}) RETURNING id INTO :id_out";

  private $searchKeywords = "SELECT id FROM kulcsszo WHERE name LIKE '%{{name}}%' AND nyelv = {{nyelv}}";

  /**
   * Keyword constructor.
   * @param $lang
   */

  public function __construct() {
    parent::__construct();
  }

  /**
   * Létrehozza a kulcsszavat
   * @param $keyword
   * @param $nyelv
   * @return mixed
   */
  public function insertKeyword($keyword, $nyelv) {
    $sql = replaceValues($this->selectKeywordsSQL, array("name" => $keyword, "nyelv" => $nyelv));
    $data = $this->query($sql)->getFetchedResult();
    if (count($data)) {
      return $data[0]["id"];
    }
    $sql = replaceValues($this->insertKeywordsSQL, array("name" => $keyword, "nyelv" => $nyelv));
    return $this->query($sql)->getId();
  }

  /**
   * @param $keywords
   * @param $nyelv
   * @return array
   */
  public function searchKeywords($keywords, $nyelv) {
    $ids = [];
    foreach ($keywords as $keyword) {
      $sql = replaceValues($this->searchKeywords, array("name" => $keyword, "nyelv" => $nyelv));
      $data = $this->query($sql)->getFetchedResult();
      foreach ($data as $row) {
        array_push($ids, $row["id"]);
      }
    }
    return $ids;
  }
}