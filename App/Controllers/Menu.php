<?php

namespace App\Controllers;

use \Core\View;

class Menu extends \Core\Controller
{
	public function mainAction()
	{
		if(!isset($_SESSION['user_id'])) {
		
		$this -> redirect('/login');
		}
		
		View::renderTemplate('Menu/main.html');
	}
}
