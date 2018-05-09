<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Keyword extends DB
{
    private $keywords = array();

    private $selectKeywordsSQL = "SELECT * FROM cikkkulcsszo WHERE nyelv = {{lang}}";

    //kostruktor

    public function __construct($lang) {
        parent::__construct();
        $keywords = $this->query(replaceValues($this->selectKeywordsSQL, array("lang" => $lang)))->getFetchedResult();
        foreach ($keywords as $keyword) {
            $this->keywords[$keyword["id"]] = $keyword["name"];
        }
    }

    //kulcszavakat adja vissza

    public function getKeywords() {
        return $this->keywords;
    }

    //kulcsszavat adja vissza id alapján

    public function getKeywordById($id) {
        return $this->keywords[$id];
    }
}