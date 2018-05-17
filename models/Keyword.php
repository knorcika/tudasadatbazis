<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Keyword extends DB {
  private $keywords = array();

  private $selectKeywordsSQL = "SELECT * FROM kulcsszo WHERE name = '{{name}}' AND nyelv = {{nyelv}}";
  private $insertKeywordsSQL = "INSERT INTO kulcsszo (name, nyelv) VALUES ('{{name}}', {{nyelv}}) RETURNING id INTO :id_out";

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
}