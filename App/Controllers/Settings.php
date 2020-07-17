<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;

class Settings extends Authenticated
{
    public function profileAction()
    {
        View::renderTemplate('Settings/profile.html');
    }

    public function incomeCategoriesAction()
    {
        $incomeCategories = Category::getCurrentUserIncomeCategories();

        View::renderTemplate('Settings/income-categories.html', [
            'incomeCategories' => $incomeCategories
        ]);
    }

    public function validateCategoryAction()
    {
        if(isset($_POST['categoryType']) &&  $_POST['categoryType'] == 'income') {
            
            $categoryExists = !Category::incomeCategoryExists($_POST['categoryNewName']);
        }

        header('Content-Type: application/json');
        
        echo json_encode($categoryExists);
    }
}
