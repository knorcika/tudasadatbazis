<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/models/Keyword.php";

class Article extends DB {
  private $getArticleByIdSQL = "SELECT * FROM cikk WHERE id = {{id}}";

  private $getArticleByUserSQL = "SELECT * FROM cikk WHERE felhasznalo = {{felhasznaloId}}";
  private $getAricleByLektorSQL = "SELECT * FROM cikk WHERE lektor = {{lektorId}}";
  private $getAricleByLangSQL = "SELECT * FROM cikk WHERE nyelv = {{nyelvId}}";

  private $getArticlesByKeywordsSQL = "SELECT * FROM cikk INNER JOIN cikkulcsszo ON cikk.id = cikkkulcsszo.cikk
                                         INNER JOIN kulcsszo ON cikkkulcsszo.kulcsszo = {{keyword}}";
  private $getArticlesByCategoriesSQL = "SELECT * FROM cikk INNER JOIN cikktemakor ON cikk.id = cikktemakor.cikk
                                         INNER JOIN cikkkulcsszo.kulcsszo = {{category}}";

  private $insertArticleSQL = "INSERT INTO cikk (nyelv, felhasznalo, cim, status, text) VALUES " .
  "({{nyelv}}, {{felhasznalo}}, '{{cim}}', '{{status}}', '{{text}}' ) RETURNING id INTO :id_out";

  private $insertCikkTemakorSQL = "INSERT INTO cikktemakor (cikk, temakor) VALUES ({{cikk}}, {{temakor}})";
  private $insertCikkKulcsszoSQL = "INSERT INTO cikkkulcsszo (cikk, kulcsszo) VALUES ({{cikk}}, {{kulcsszo}})";

  private $updateLektorIdSQL = "UPDATE cikk SET lektor = {{lektorId}} WHERE id = {{id}}";

  private $searchArticleSQL = "SELECT * FROM cikk INNER JOIN cikkkulcsszo ON cikk.id = cikkkulcsszo.cikk
  INNER JOIN cikktemakor ON cikk.id = cikktemakor.cikk
  WHERE cikk.status = '{{status}}'";

  public function __construct() {
    parent::__construct();
  }

  /**
   * adott cikk lekérdezése
   * @param $article
   * @return array
   */

  public function getArticleById($id) {
    $sql = replaceValues($this->getArticleByIdSQL, array("id" => $id));
    return $this->query($sql)->getFetchedResult();
  }

  /**
   * Az adott felhasználó által írt cikkek lekérdezése
   * @param $article
   * @return array
   */

  public function getArticelsByUser($userid) {
    $sql = replaceValues($this->getArticleByUserSQL, array("userid" => $userid));
    return $this->query($sql)->getFetchedResult();
  }

  /** Az adott lektor által validált cikkek lekérdezése
   * @param $article
   * @return array
   */

  public function getArticelsByLektor($lectorid) {
    $sql = replaceValues($this->getAricleByLektorSQL, array("lectorid" => $lectorid));
    return $this->query($sql)->getFetchedResult();
  }

  /**
   * Adott nyelvű cikkek lekérdezése
   * @param $article
   * @return array
   */

  public function getArticelsByNyelv($nyelvid) {
    $sql = replaceValues($this->getAricleByLangSQL, array("nyelvid" => $nyelvid));
    return $this->query($sql)->getFetchedResult();
  }

  /**
   * Cikkek lekérése az adott kulcsszóra
   * @param $article
   * @return array
   */

  public function getArticlesByKeywords($keyword) {
    $sql = replaceValues($this->getArticlesByKeywordsSQL, array("keyword" => $keyword));
    return $this->query($sql)->getFetchedResult();
  }

  /**
   * Cikkek lekérése adott kategória alapján     *
   * @param $article
   * @return array
   */

  public function getArticlesByCategoriesSQL($category) {
    $sql = replaceValues($this->getArticlesByCategoriesSQL, $category);
    return $this->query($sql)->getFetchedResult();
  }

    /** Lektor hozzárendelése egy cikkhez
     * @param $lektorId
     * @param $articleId
     * @return array
     */

  public function updateLektorIdSQL($lektorId, $articleId) {
      $sql = replaceValues($this->updateLektorIdSQL, array("lektor" => $lektorId, "id" => $articleId));
      return $this->query($sql)->getFetchedResult();
  }

  /**
   * Új cikk létrehozása
   * @param $article
   * @return boolean
   */
  public function insertArticle($article) {
    global $constants;
    $article["status"] = $constants["ARTICLE_OPEN"];
    $sql = replaceValues($this->insertArticleSQL, $article);
    $id = $this->query($sql)->getId();
    if (!$id) {
      return false;
    }
    foreach ($article["temakorok"] as $temakor) {
      $sql = replaceValues($this->insertCikkTemakorSQL, array("cikk" => $id, "temakor" => $temakor));
      $this->query($sql)->getResult();
    }
    $keywords = strtolower($article["keywords"] . " " . $article["cim"]);
    $keywords = str_replace("-", " ", $keywords);
    $keywords = preg_replace('/[^A-Za-z0-9öüóőúéáűí ]/', '', $keywords);
    $keywords = explode(" ", $keywords);
    if (count($keywords)) {
      $keywordModel = new Keyword();
      foreach ($keywords as $keyword) {
        $keywordId = $keywordModel->insertKeyword($keyword, $article["nyelv"]);
        $sql = replaceValues($this->insertCikkKulcsszoSQL, array("cikk" => $id, "kulcsszo" => $keywordId));
        $this->query($sql)->getResult();
      }
    }
    return true;
  }

  /**
   * @param $keyword
   * @param $nyelvid
   * @param $topics
   * @return array
   */
  public function search($keyword, $nyelvid, $topics) {
    global $constants;
    $sql = replaceValues($this->searchArticleSQL, array("status" => $constants["ARTICLE_APPROVED"]));
    if ($keyword) {
      $keywordModel = new Keyword();
      $keywords = strtolower($keyword);
      $keywords = explode(" ", $keywords);
      $ids = $keywordModel->searchKeywords($keywords, $nyelvid);
      if (count($ids)) {
        $sqlQuery = " AND cikkkulcsszo.kulcsszo IN ({{keywordids}})";
        $ids = implode(", ", $ids);
        $sql .= replaceValues($sqlQuery, array("keywordids" => $ids));
      }
    }

    if (count($topics)) {
      $sqlQuery = " AND cikktemakor.temakor IN ({{temakorids}})";
      $temakorids = implode(", ", $topics);
      $sql .= replaceValues($sqlQuery, array("temakorids" => $temakorids));
    }
    $sql .= " ORDER BY id DESC";
    $data = $this->query($sql)->getFetchedResult();
    $res = array();
    foreach ($data as $row) {
      if (!isset($res[$row["id"]])) {
        $res[$row["id"]] = $row;
      }
    }
    return $res;
  }
}