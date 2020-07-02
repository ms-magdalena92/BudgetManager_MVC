<?php

namespace App\Controllers;

use \Core\View;

class Income extends Authenticated
{
    public function newAction()
    {
        View::renderTemplate('Income/new-income.html');
    }
}
