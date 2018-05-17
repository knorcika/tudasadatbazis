<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";
require_once BASE_DIR . "/models/Category.php";
require_once BASE_DIR . "/models/Topic.php";
require_once BASE_DIR . "/models/Article.php";

class Search {
  private $page = "";
  private $user;
  private $lang;

  /**
   * Search constructor.
   * @param $page
   * @param $user
   * @param $lang
   */
  public function __construct($page, $user, $lang) {
    $this->page = $page;
    $this->user = $user;
    $this->lang = $lang;
  }

  public function getBody() {
    global $_POST, $constants;
    $message = "";
    $data = array(
      "keywords" => ""
    );
    $article = new Article();
    if (isset($_POST['search']) && (isset($_POST["keywords"]) || isset($_POST["temakorok"]))) {
      $data = $_POST;
    }
    $keywords = isset($_POST["keywords"]) ? $_POST["keywords"] : "";
    $temakorok = isset($_POST["temakorok"]) ? $_POST["temakorok"] : array();
    $articles = $article->search($keywords, $this->lang, $temakorok);

    $data["categories"] = $this->getCategories();
    $data["articles"] = $this->getArticles($articles);
    $data["message"] = $message;
    $view = getView('search/search.html');
    $view = replaceValues($view, $data);
    return $view;
  }

  private function getCategories() {
    $result = "";
    $categories = new Category($this->lang);
    $categories = $categories->getCategories();
    $view = getView('new_article/categories.html');
    foreach ($categories as $key => $val) {
      $data = array(
        "id" => $key,
        "name" => $val,
        "topics" => $this->getTopics($key)
      );
      $category = replaceValues($view, $data);
      $result .= $category . PHP_EOL;
    }
    return $result;
  }

  private function getTopics($catId) {
    $result = "";
    $topic = new Topic();
    $topics = $topic->getTopicsByCategory($catId);
    $view = getView('new_article/topics.html');
    foreach ($topics as $key) {
      $data = array(
        "id" => $key,
        "name" => $topic->getTopicById($key),
      );
      $topicView = replaceValues($view, $data);
      $result .= $topicView . PHP_EOL;
    }
    return $result;
  }

  private function getArticles($articles) {
    $result = "";
    $view = getView('article/article.html');
    foreach ($articles as $article) {
      $articleView = replaceValues($view, $article);
      $result .= $articleView . PHP_EOL;
    }
    return $result;
  }
}