<?php

namespace App\Controllers;

use \App\Models\User;

class Account extends \Core\Controller
{
    //(AJAX) validate if email is available
    public function validateEmailAction()
    {
        $isEmailValid = !User::emailExists($_GET['email']);
        
        header('Content-Type: application/json');
        
        echo json_encode($isEmailValid);
    }
}
