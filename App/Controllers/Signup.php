<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Category;

class Signup extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }
    
    public function createAction()
    {
        $user = new User($_POST);
        
        if($user -> saveUserToDB()) {
            
            Category::assignDefaultCategoriesToNewUser();
            
            $user -> sendActivationEmail();
            
            $this -> redirect('/signup/success');
            
        } else {
            
            View::renderTemplate('Signup/new.html', ['user' => $user]);
        }
    }
    
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
    
    public function activateAction()
    {
        User::activateAccount($this -> routeParams['token']);
        $this -> redirect('/signup/activated');
    }
    
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}
