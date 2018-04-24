<?php
require_once BASE_DIR . "/config/menuPoints.php";
require_once BASE_DIR . "/services/replaceValues.php";

class Menu {
  private $page = "";
  private $user;

  /**
   * Menu constructor.
   * @param $page
   * @param $user
   */
  public function __construct($page, $user) {
    $this->page = $page;
    $this->user = $user;
  }

  /**
   * Menu generálása
   * @return string
   */
  public function getMenu() {
    $view = file_get_contents(BASE_DIR . "/public/views/menu/menuBody.html");
    $menuPoints = $this->getMenuPoints();
    $menu = str_replace("{{menuitems}}", $menuPoints, $view);
    return $menu;
  }

  /**
   * Menüpontok hozzáadása
   * @return string
   */
  private function getMenuPoints() {
    global $menuPoints;
    $view = file_get_contents(BASE_DIR . "/public/views/menu/menuItems.html");
    $menus = "";
    $roles = $this->user->getRoles();
    foreach ($menuPoints as $menuPoint) {
      $shouldAdd = false;
      $menuPoint["classes"] = "";
      $menu = "";
      foreach ($roles as $role) {
        if (in_array($role, $menuPoint["roles"])) $shouldAdd = true;
      }
      if ($menuPoint["page"] === $this->page) {
        $menuPoint["classes"] .= " active ";
      }
      if ($shouldAdd) {
        $menu = replaceValues($view, $menuPoint);
      }
      $menus .= $menu . PHP_EOL;
    }
    return $menus;
  }
}