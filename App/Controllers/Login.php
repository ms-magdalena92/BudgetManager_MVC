<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
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

		if ($user) {
			
			Auth::login($user);
			$this -> redirect(Auth::getReturnToPage());
			
		} else {
			
			View::renderTemplate('Login/new.html', [
			'email' => $_POST['email']
			]);
		}
	}
	
	public function logoutAction()
	{
		Auth::logout();
		
		$this -> redirect('/');
	}
}
