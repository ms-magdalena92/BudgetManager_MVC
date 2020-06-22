<?php

spl_autoload_register(function ($class) {
    
	$root = dirname(__DIR__);
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
	
    if (is_readable($file)) {
		
        require $root . '/' . str_replace('\\', '/', $class) . '.php';
    }
});
	
$router = new Core\Router();

$router -> addRoute('{controller}/{action}');
	
$url = $_SERVER['QUERY_STRING'];
$router -> dispatchRoute($url);

/*if ($router -> matchUrlToRoutes($url)) {
	
	echo '<pre>';
	var_dump($router -> getRouteParams());
	echo '</pre>';
	
} else {
	
	echo "No route found for URL '$url'";
}*/