<?php

namespace Core;

abstract class Controller
{
    protected $routeParams = [];

    public function __construct($routeParams)
    {
        $this->routeParams = $routeParams;
    }
	
    public function __call($name, $args)
    {
        $method = $name.'Action';

        if (method_exists($this, $method)) {
            if ($this -> before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this -> after();
            }
        } else {
            throw new \Exception("Method $method not found in the controller ".get_class($this));
        }
    }
	
	public function redirect($url)
	{
		header('Location: http://'.$_SERVER['HTTP_HOST'].$url, true, 303);
		exit;
	}
	
    protected function before()
    {
    }

    protected function after()
    {
    }
}
