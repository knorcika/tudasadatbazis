<?php
require_once BASE_DIR . "/config/constants.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Page {

  private $menu;
  private $body;
  private $footer;

  /**
   * Page constructor.
   * @param $menu
   * @param $body
   * @param $footer
   */
  public function __construct($menu, $body, $footer) {
    $this->menu = $menu;
    $this->body = $body;
    $this->footer = $footer;
  }

  /**
   * A teljes oldal kigenerálásáért felelős metódus
   * @return mixed
   */
  public function getPage() {
    global $constants;
    $view = file_get_contents(BASE_DIR . "/public/views/index.html");
    $page = str_replace("{{menu}}", $this->menu, $view);
    $page = str_replace("{{body}}", $this->body, $page);
    $page = str_replace("{{footer}}", $this->footer, $page);
    $page = replaceValues($page, $constants);
    return $page;
  }
}