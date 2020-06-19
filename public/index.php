<?php

require '../Core/Router.php';
	
$router = new Core\Router();

$router -> addRoute('{controller}/{action}');
	
$url = $_SERVER['QUERY_STRING'];

if ($router -> matchUrlToRoutes($url)) {
	
	echo '<pre>';
	var_dump($router -> getRouteParams());
	echo '</pre>';
	
} else {
	
	echo "No route found for URL '$url'";
}