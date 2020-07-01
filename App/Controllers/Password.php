<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Password extends \Core\Controller
{
	public function forgotAction()
	{
		View::renderTemplate('Password/forgot.html');
	}
	
	public function requestResetAction()
	{
		User::requestPasswordReset($_POST['email']);
		View::renderTemplate('Password/reset_request_submitted.html');
    }
}
