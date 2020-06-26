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

    public function dispatchRoute($url)
    {
        $url = $this->removeQueryStringVariables($url);
		
		if ($this->matchUrlToRoutes($url)) {
			
			$this -> createControlerAndRunActionMethod();
            
        } else {
            
			throw new \Exception('No route matched.', 404);
        }
    }
	
	protected function createControlerAndRunActionMethod()
	{
		$controller = $this -> routeParams['controller'];
        $controller = $this -> convertToStudlyCaps($controller);
        $controller = "App\Controllers\\$controller";

        if (class_exists($controller)) {
				
            $controller_object = new $controller($this->routeParams);

            $action = $this -> routeParams['action'];
            $action = $this -> convertToCamelCase($action);

            if (preg_match('/action$/i', $action) == 0) {
					
                $controller_object -> $action();

            } else {

			    throw new \Exception("Method $action (in controller $controller) not found");
            }
				
        } else {
				
	        throw new \Exception("Controller class $controller not found");
        }
    }

    //e.g. post-authors => PostAuthors
    protected function convertToStudlyCaps($string)
    {
		return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    //e.g. add-new => addNew
    protected function convertToCamelCase($string)
    {
		return lcfirst($this->convertToStudlyCaps($string));
    }
	
	protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
			
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
				
                $url = $parts[0];
				
            } else {
				
                $url = '';
            }
        }
		
        return $url;
    }
}
