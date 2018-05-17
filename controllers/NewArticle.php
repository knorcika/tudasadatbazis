<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";
require_once BASE_DIR . "/services/getView.php";
require_once BASE_DIR . "/models/Category.php";
require_once BASE_DIR . "/models/Topic.php";
require_once BASE_DIR . "/models/Article.php";

class NewArticle {
  private $page = "";
  private $user;
  private $lang;

  /**
   * NewArticle constructor.
   * @param $page
   * @param $user
   * @param $lang
   * @param $article
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
      "cim" => "",
      "text" => "",
      "keywords" => ""
    );
    if (isset($_POST['new_article'])) {
      $user = $this->user->toArray();
      $data = $_POST;
      $data["felhasznalo"] = $user["id"];
      $data["nyelv"] = $this->lang;
      $article = new Article();
      if ($article->insertArticle($data)) {
        $data = array(
          "cim" => "",
          "text" => "",
          "keywords" => ""
        );
        $message = $constants["NEW_ARTICLE_SUCCESS"];
      }
    }

    $data["categories"] = $this->getCategories();
    $data["message"] = $message;
    $view = getView('new_article/new_article.html');
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
}