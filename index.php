<?php
define('BASE_DIR', dirname(__FILE__));
require_once BASE_DIR . '/models/User.php';
require_once BASE_DIR . '/models/Lang.php';
require_once BASE_DIR . '/controllers/Page.php';
require_once BASE_DIR . '/controllers/Menu.php';

$page = "";
$languages = new Lang();
$lang = $languages->getDefaultLanguage();
if (isset($_GET["page"])) $page = $_GET["page"];
if (isset($_GET["lang"])) $lang = $_GET["lang"];
$user = new User(array());
/**
 * Router goes here
 */

$menu = new Menu($page, $user, $lang);
$page = new Page($menu->getMenu(), "", "");
echo $page->getPage();