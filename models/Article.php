<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Article extends DB
{
    private $getArticleByIdSQL = "SELECT * FROM cikk WHERE id = {{id}}";

    private $getArticleByUserSQL = "SELECT * FROM cikk WHERE felhasznalo = {{felhasznaloId}}";
    private $getAricleByLektorSQL =  "SELECT * FROM cikk WHERE lektor = {{lektorId}}";
    private $getAricleByLangSQL =  "SELECT * FROM cikk WHERE nyelv = {{nyelvId}}";

    private $getArticlesByKeywordsSQL = "SELECT * FROM cikk INNER JOIN cikkulcsszo ON cikk.id = cikkkulcsszo.cikk
                                         INNER JOIN kulcsszo ON cikkkulcsszo.kulcsszo = {{keyword}}";
    private $getArticlesByCategoriesSQL = "SELECT * FROM cikk INNER JOIN cikktemakor ON cikk.id = cikktemakor.cikk
                                         INNER JOIN cikkkulcsszo.kulcsszo = {{category}}";

  private $insertArticleSQL = "INSERT INTO cikk (nyelv, felhasznalo, cim, status, text) VALUES " .
  "({{nyelv}}, {{felhasznalo}}, '{{cim}}', '{{status}}', '{{text}}' )";

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

  /**
   * Új cikk létrehozása
   * @param $article
   * @return mixed
   */
  public function insertArticle($article) {
    global $constants;
    $article["status"] = $constants["ARTICLE_OPEN"];
    $sql = replaceValues($this->insertArticleSQL, $article);
    return $this->query($sql)->getResult();
  }
}