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
$router -> addRoute('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router -> addRoute('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router -> addRoute('settings/activate-email/{token:[\da-f]+}', ['controller' => 'Settings', 'action' => 'activate-email']);

$url = $_SERVER['QUERY_STRING'];
$router -> dispatchRoute($url);
