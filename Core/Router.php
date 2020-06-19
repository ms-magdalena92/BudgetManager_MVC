<?php

namespace Core;

class Router {
	
    protected $routes = [];
	
    protected $routeParams = [];

	protected function changeIntoRegEx($route) 
	{
		$route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = '/^' . $route . '$/i';
		
		return $route;
	}

	public function addRoute($route, $params = [])
    {
        $route = $this->changeIntoRegEx($route);
        $this -> routes[$route] = $params;
    }
	
    public function getRoutes()
    {
        return $this -> routes;
    }

    public function matchUrlToRoutes($url)
    {
        foreach ($this -> routes as $route => $params) {
			
            if (preg_match($route, $url, $matches)) {
                
                foreach ($matches as $paramName => $value) {
					
                    if (is_string($paramName)) {
						
                        $params[$paramName] = $value;
                    }
                }
				
                $this -> routeParams = $params;
				
                return true;
            }
        }
		
        return false;
    }

    public function getRouteParams()
    {
        return $this -> routeParams;
    }
}
