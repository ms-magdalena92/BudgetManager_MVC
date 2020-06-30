<?php

namespace App;

use \App\Models\User;

class Auth
{
	public static function login($user, $rememberMe)
	{
		session_regenerate_id(true);

		$_SESSION['user_id'] = $user -> user_id;
		
		if($rememberMe) {
			
			$user -> rememberLogin();
		}
	}

	public static function logout()
	{
		$_SESSION = [];

		if (ini_get('session.use_cookies')) {
		  
			$params = session_get_cookie_params();

			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params['path'],
				$params['domain'],
				$params['secure'],
				$params['httponly']
			);
		}
	  
		session_destroy();
	}
	
	public static function getLoggedUser()
	{
		if(isset($_SESSION['user_id'])) {
		
		return User::findUserByID($_SESSION['user_id']);
		}
	}
	
	public static function rememberRequestedURL()
	{
		$_SESSION['requested_page'] = $_SERVER['REQUEST_URI'];
	}
	
	public static function getReturnToPage()
	{
		return $_SESSION['requested_page'] ?? '/menu/main';
	}
}
