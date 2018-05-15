<?php
define('BASE_DIR', dirname(__FILE__));
require_once BASE_DIR . '/models/User.php';
require_once BASE_DIR . '/models/Lang.php';
require_once BASE_DIR . '/controllers/Page.php';
require_once BASE_DIR . '/controllers/Menu.php';
require_once BASE_DIR . '/controllers/Register.php';

$page = "index";
$body = "";
$languages = new Lang();
$lang = $languages->getDefaultLanguage();
if (isset($_GET["page"])) $page = $_GET["page"];
if (isset($_GET["lang"])) $lang = $_GET["lang"];
$user = new User(array());

$router = array(
  "register" => "Register"
);

if (isset($router[$page])) {
  $body = new $router[$page]($page, $user, $lang);
  $body = $body->getBody();
}

$menu = new Menu($page, $user, $lang);
$page = new Page($menu->getMenu(), $body, "");
echo $page->getPage();
