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
        
        $currentView = strstr($template, '/', true);
        $currentPage = strstr($template, '/');
        
        if ($twig === null) {
            
            $loader = new \Twig\Loader\FilesystemLoader('../App/Views');
            $twig = new \Twig\Environment($loader);
            
            $twig -> addGlobal('current_user', \App\Auth::getLoggedUser());
            $twig -> addGlobal('flash_messages', \App\Flash::getFlashMsg());
            $twig -> addGlobal('current_date', \App\Date::getCurrentDate());
            $twig -> addGlobal('current_view', $currentView);
            $twig -> addGlobal('current_page', $currentPage);
        }
        
        return $twig -> render($template, $args);
    }
    
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }
}
