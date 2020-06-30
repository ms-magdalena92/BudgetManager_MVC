<?php

namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;

class Auth
{
	public static function login($user, $rememberMe)
	{
		session_regenerate_id(true);

		$_SESSION['user_id'] = $user -> user_id;
		
		if($rememberMe) {
			
			if($user -> rememberLogin()) {
				
				setcookie('remember_me', $user -> remember_token, $user -> expiry_timestamp, '/');
			}
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
		
		static::forgetLogin();
	}
	
	public static function getLoggedUser()
	{
		if(isset($_SESSION['user_id'])) {
			
			return User::findUserByID($_SESSION['user_id']);
			
		} else {
			
			return static::loginFromRememberedCookie();
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
	
	protected static function loginFromRememberedCookie()
	{
		$cookie = $_COOKIE['remember_me'] ?? false;
		
		if($cookie) {
			
			$rememberedLogin = RememberedLogin::findUserByToken($cookie);
			
			if($rememberedLogin && !$rememberedLogin -> tokenExpired()) {
				
				$user = $rememberedLogin -> getRememberedUser();
				
				static::login($user, false);
				
				return $user;
			}
        }
    }
	
	protected static function forgetLogin()
	{
		$cookie = $_COOKIE['remember_me'] ?? false;
		
		if($cookie) {
			
			$remembered_login = RememberedLogin::findUserByToken($cookie);
			
			if($remembered_login) {
				
				$remembered_login -> deleteRememberedLogin();
			}
			
			setcookie('remember_me', '', time() - 3600);
		}
	}
}
