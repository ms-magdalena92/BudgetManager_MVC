<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

class Password extends \Core\Controller
{
    public function forgotAction()
    {
        View::renderTemplate('Password/forgot.html');
    }
    
    public function requestResetAction()
    {
        if(User::requestPasswordReset($_POST['email'])) {
            
            View::renderTemplate('Password/reset_request_submitted.html');
            
        } else {
            
            Flash::addFlashMsg('Reset password e-mail wasn\'t delivered to '.$_POST['email'].' because the account doesn\'t exist.', Flash::WARNING);
            View::renderTemplate('Password/forgot.html');
        }
    }
    
    public function resetAction()
	{
        $token = $this -> routeParams['token'];
        
        $user = $this -> getUserOrExit($token);
        
        View::renderTemplate('Password/reset.html', [
            'token' => $token
        ]);
    }
    
    protected function getUserOrExit($token)
    {
        $user = User::findUserByResetToken($token);
        
        if ($user) {
            
            return $user;
            
        } else {
            
            View::renderTemplate('Password/invalid_token.html');
            exit;
        }
    }
    
    public function resetPasswordAction()
    {
        $token = $_POST['token'];
        $user = $this -> getUserOrExit($token);
        
        if ($user -> resetPassword($_POST['password'])) {
            
            View::renderTemplate('Password/successful_reset.html');
            
        } else {
            
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }
}
