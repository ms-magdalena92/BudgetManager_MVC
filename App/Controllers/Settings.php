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

    public function validateCategoryAction()
    {
        if(isset($_POST['categoryType']) &&  $_POST['categoryType'] == 'income') {
            
            $categoryExists = !Categories::incomeCategoryExists($_POST['categoryName']);
        }

        header('Content-Type: application/json');
        
        echo json_encode($categoryExists);
    }
}
