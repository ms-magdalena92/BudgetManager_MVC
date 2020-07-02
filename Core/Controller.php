<?php

namespace Core;

use \App\Auth;
use \App\Flash;

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
    
    public function requireLogin()
    {
        if (!Auth::getLoggedUser()) {
            
            Flash::addFlashMsg('Please log in to accesss that page.', Flash::INFO);
            Auth::rememberRequestedURL();
            $this -> redirect('/login');
        }
    }
}
