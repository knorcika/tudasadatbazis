<?php
define('BASE_DIR', dirname(__FILE__));
require_once BASE_DIR . '/controllers/Page.php';

/**
 * Router goes here
 */

$page = new Page("", "", "");
echo $page->getPage();
