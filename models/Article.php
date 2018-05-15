<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Article extends DB
{
    private $getArticleByIdSQL = "SELECT * FROM cikk WHERE id = {{id}}";

    private $getArticleByUserSQL = "SELECT * FROM cikk WHERE felhasznalo = {{felhasznalo}}";
    private $getAricleByLektorSQL =  "SELECT * FROM cikk WHERE lektor = {{lektor}}";
    private $getAricleByLangSQL =  "SELECT * FROM cikk WHERE nyelv = {{lang}}";

    private $getArticlesByKeywordsSQL = "SELECT * FROM cikk INNER JOIN cikkulcsszo ON cikk.id = cikkkulcsszo.cikk
                                         INNER JOIN kulcsszo ON cikkkulcsszo.kulcsszo = {{keyword}}";
    private $getArticlesByCategoriesSQL = "SELECT * FROM cikk INNER JOIN cikktemakor ON cikk.id = cikktemakor.cikk
                                         INNER JOIN cikkkulcsszo.kulcsszo = {{categories}}";

    private $insertArticleSQL = "INSERT INTO cikk (nyelv, felhasznalo, lektor, cim, status, role) VALUES " .
"('{{lang}}', '{{felhasznalo}}', '{{lektor}}', '{{cim}}', '{{status}}', '{{role}}' )";

    public function __construct() {
        parent::__construct();
    }

    /**
     * adott cikk lekérdezése
     * @param $article
     * @return array
     */

    public function getArticleById($article) {
        $sql = replaceValues($this->getArticleByIdSQL, $article);
        return $this->query($sql)->getFetchedResult();
    }

    /**
     * Az adott felhasználó által írt cikkek lekérdezése
     * @param $article
     * @return array
     */

    public function getArticelsByUser($article) {
        $sql = replaceValues($this->getArticleByUserSQL, $article);
        return $this->query($sql)->getFetchedResult();
    }

    /** Az adott lektor által validált cikkek lekérdezése
     * @param $article
     * @return array
     */

    public function getArticelsByLektor($article) {
        $sql = replaceValues($this->getAricleByLektorSQL, $article);
        return $this->query($sql)->getFetchedResult();
    }

    /**
     * Adott nyelvű cikkek lekérdezése
     * @param $article
     * @return array
     */

    public function getArticelsByLang($article) {
        $sql = replaceValues($this->getAricleByLangSQL, $article);
        return $this->query($sql)->getFetchedResult();
    }

    /**
     * Cikkek lekérése az adott kulcsszóra
     * @param $article
     * @return array
     */

    public function getArticlesByKeywords($article) {
        $sql = replaceValues($this->getArticlesByKeywordsSQL, $article);
        return $this->query($sql)->getFetchedResult();
    }

    /**
     * Cikkek lekérése adott kategória alapján     *
     * @param $article
     * @return array
     */

    public function getArticlesByCategoriesSQL($article) {
        $sql = replaceValues($this->getArticlesByCategoriesSQL, $article);
        return $this->query($sql)->getFetchedResult();
    }
}