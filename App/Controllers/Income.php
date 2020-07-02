<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

class Income extends Authenticated
{
    public function newAction()
    {
        $incomeCategories = Categories::getCurrentUserIncomeCategories();
        
        View::renderTemplate('Income/new-income.html', array('incomeCategories' => $incomeCategories));
    }
}
