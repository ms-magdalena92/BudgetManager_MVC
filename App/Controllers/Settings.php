<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

class Settings extends Authenticated
{
    public function profileAction()
    {
        View::renderTemplate('Settings/profile.html');
    }

    public function incomeCategoriesAction()
    {
        $incomeCategories = Categories::getCurrentUserIncomeCategories();

        View::renderTemplate('Settings/income-categories.html', [
            'incomeCategories' => $incomeCategories
        ]);
    }
}
