<?php

namespace Core;

class View
{
    public static function renderView($view, $args = [])
    {
        extract($args, EXTR_SKIP);
		
	    $file = "../App/Views/$view";

        if (is_readable($file)) {
			
            require $file;
			
        } else {
			
            throw new \Exception("$file not found");
        }
    }
	
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
			
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
			$twig -> addGlobal('is_registered', \App\Models\User::isRegistered());
			$twig -> addGlobal('current_user', \App\Auth::getLoggedUser());
			$twig -> addGlobal('flash_messages', \App\Flash::getFlashMsg());
        }
		
        echo $twig->render($template, $args);
    }
}
