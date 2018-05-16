<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";

class New_Article
{
    private $page = "";
    private $user;
    private $lang;
    private $article;

    /**
     * New_Article constructor.
     * @param $page
     * @param $user
     * @param $lang
     * @param $article
     */
    public function __construct($page, $user, $lang, $article) {
        $this->page = $page;
        $this->user = $user;
        $this->lang = $lang;
        $this->article = $article;
    }

    public function getBody() {
        global $_POST;
        $message = "";
        if (isset($_POST['new_article'])) {
            $this->article->insertArticle($_POST);
        }
        $view = getView('new_article.html');
        return $view;
    }
}