<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";

class New_Article
{
    private $page = "";
    private $user;
    private $lang;

    /**
     * Menu constructor.
     * @param $page
     * @param $user
     */
    public function __construct($page, $user, $lang) {
        $this->page = $page;
        $this->user = $user;
        $this->lang = $lang;
    }

    public function getBody() {
        global $_POST;
    }
}