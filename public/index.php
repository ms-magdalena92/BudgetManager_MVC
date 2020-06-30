<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

session_start();
	
$router = new Core\Router();

$router -> addRoute('', ['controller' => 'Home', 'action' => 'index']);
$router -> addRoute('login', ['controller' => 'Login', 'action' => 'new']);
$router -> addRoute('logout', ['controller' => 'Login', 'action' => 'logout']);
$router -> addRoute('{controller}/{action}');
	
$url = $_SERVER['QUERY_STRING'];
$router -> dispatchRoute($url);
