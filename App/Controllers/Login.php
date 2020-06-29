<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

class Login extends \Core\Controller
{
	public function newAction()
	{
		View::renderTemplate('Login/new.html');
	}
	
	public function create()
	{
		$user = User::authenticate($_POST['email'], $_POST['password']);

		if ($user) {
			
			echo 'Logged in';
			
		} else {
			
			View::renderTemplate('Login/new.html', [
			'email' => $_POST['email']
			]);
		}
	}
}
