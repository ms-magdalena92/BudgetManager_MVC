<?php

namespace App\Controllers;

use \Core\View;

class Settings extends Authenticated
{
    public function profileAction()
    {
        View::renderTemplate('Settings/profile.html');
    }
}
