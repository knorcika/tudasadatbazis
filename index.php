<?php
define('BASE_DIR', dirname(__FILE__));
require_once BASE_DIR . '/controllers/Page.php';
require_once BASE_DIR . '/controllers/Menu.php';
require_once BASE_DIR . '/models/User.php';
$page = "";
if (isset($_GET["page"])) $page = $_GET["page"];
$user = new User(array());
/**
 * Router goes here
 */

$menu = new Menu($page, $user);
$page = new Page($menu->getMenu(), "", "");
echo $page->getPage();
