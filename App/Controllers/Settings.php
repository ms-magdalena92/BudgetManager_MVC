<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Flash;

class Settings extends Authenticated
{
    protected static function getIncomeCategories()
    {
        return Category::getCurrentUserIncomeCategories();
    }
    
    public function profileAction()
    {
        View::renderTemplate('Settings/profile.html');
    }

    public function incomeCategoriesAction()
    {
        $incomeCategories = self::getIncomeCategories();

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

    public function editIncomeCategoryAction()
    {
        $incomeCategory = new Category($_POST);
        
        if($incomeCategory -> editIncomeCategory()) {
            
            Flash::addFlashMsg('Your category has been successfully edited.');
            $this -> redirect('/settings/income-categories');
            
        } else {
            
            $incomeCategories = self::getIncomeCategories();

            View::renderTemplate('Settings/income-categories.html', [
                'incomeCategories' => $incomeCategories,
                'incomeCategory' => $incomeCategory
            ]);
        }
    }
}
