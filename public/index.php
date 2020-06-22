<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
	
$router = new Core\Router();

$router -> addRoute('{controller}/{action}');
	
$url = $_SERVER['QUERY_STRING'];
$router -> dispatchRoute($url);
