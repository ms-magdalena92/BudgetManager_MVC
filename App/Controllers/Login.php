<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;

class Login extends \Core\Controller
{
    public function newAction()
    {
        if (Auth::getLoggedUser()){
            
            $this -> redirect(Auth::getReturnToPage());
        }
        
        View::renderTemplate('Login/new.html');
    }
    
    public function create()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
        $rememberMe = isset($_POST['remember_me']);
        
        if ($user) {
            
            if(!$user -> is_active) {
                
                Flash::addFlashMsg('Your account is not active. Please check your email to activate your account.', Flash::WARNING);
                View::renderTemplate('Login/new.html');
            } else {
                
                Auth::login($user, $rememberMe);
                Flash::addFlashMsg('You\'ve successfully logged in.');
                $this -> redirect(Auth::getReturnToPage());
            }
        } else {
            
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $rememberMe
            ]);
        }
    }
    
    public function logoutAction()
    {
        Auth::logout();
        $this -> redirect('/login/show-logout-flash-msg');
    }
    
    public function showLogoutFlashMsgAction()
    {
        Flash::addFlashMsg('You\'ve successfully logged out.');
        $this -> redirect('/');
    }
}
