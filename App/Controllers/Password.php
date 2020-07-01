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
}
