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
    
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;
        
        if ($twig === null) {
            
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            $twig -> addGlobal('current_user', \App\Auth::getLoggedUser());
            $twig -> addGlobal('flash_messages', \App\Flash::getFlashMsg());
        }
        
        return $twig -> render($template, $args);
    }
    
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }
}
