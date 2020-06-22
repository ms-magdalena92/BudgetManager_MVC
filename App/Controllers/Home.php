<?php

namespace App\Controllers;

class Home extends \Core\Controller
{
    public function indexAction()
    {
        echo 'Home controller';
    }
	
	protected function before()
	{
	    echo "(before action filter) ";
	}
	
	protected function after()
	{
	    echo " (after action filter)";
	}
}
