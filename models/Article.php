<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Article extends DB
{
    private $id = "";
    private $cim = "";
    private $text = "";
    private $status = "";

    private $felhasznalo = "";
    private $lektor = "";
    private $nyelv = "";

    private $keywords = array();
    private $categories = array();
    private $topics = array();

    private $getArticleByUserSQL = "SELECT * FROM cikk WHERE felhasznalo = {{felhasznalo}}";
    private $getAricleByLektorSQL =  "SELECT * FROM cikk WHERE lektor = {{lektor}}";
    private $getAricleByLangSQL =  "SELECT * FROM cikk WHERE nyelv = {{lang}}";

    private $getArticlesByKeywordsSQL = "SELECT * FROM cikk INNER JOIN cikkulcsszo ON cikk.id = cikkkulcsszo.cikk
                                         INNER JOIN kulcsszo ON cikkkulcsszo.kulcsszo = {{keyword}}";
    private $getArticlesByCategoriesSQL = "SELECT * FROM cikk INNER JOIN cikktemakor ON cikk.id = cikktemakor.cikk
                                         INNER JOIN cikkkulcsszo.kulcsszo = {{categories}}";

    private $insertArticleSQL = "INSERT INTO cikk (nyelv, felhasznalo, lektor, cim, status, role) VALUES " .
"('{{lang}}', '{{felhasznalo}}', '{{lektor}}', '{{cim}}', '{{status}}', '{{role}}' )";
}