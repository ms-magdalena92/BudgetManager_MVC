<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Menu extends \Core\Controller
{
	public function mainAction()
	{
		if(!isset($_SESSION['user_id'])) {
			
			Auth::rememberRequestedURL();
			$this -> redirect('/login');
		}
		
		View::renderTemplate('Menu/main.html');
	}
}
