<?php

namespace App;

use \App\Models\User;

class Auth
{
	public static function login($user)
	{
		session_regenerate_id(true);

		$_SESSION['user_id'] = $user -> user_id;
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
}
