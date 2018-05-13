<?php
require_once BASE_DIR . "/services/DB.php";
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Keyword extends DB
{
    private $keywords = array();

    private $selectKeywordsSQL = "SELECT * FROM cikkkulcsszo WHERE nyelv = {{lang}}";

    /**
     * Keyword constructor.
     * @param $lang
     */

    public function __construct($lang) {
        parent::__construct();
        $keywords = $this->query(replaceValues($this->selectKeywordsSQL, array("lang" => $lang)))->getFetchedResult();
        foreach ($keywords as $keyword) {
            $this->keywords[$keyword["id"]] = $keyword["name"];
        }
    }

    /**
     * kulcszavakat adja vissza
     * @return array
     */

    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * kulcsszavat adja vissza id alapján
     * @param $id
     * @return mixed
     */

    public function getKeywordById($id) {
        return $this->keywords[$id];
    }
}