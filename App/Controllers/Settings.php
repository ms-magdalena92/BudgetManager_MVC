<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Models\IncomeCategory;
use \App\Models\Income;
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
            
            $categoryExists = !IncomeCategory::incomeCategoryIsAssignedToUser($_POST['categoryNewName']);
        }

        header('Content-Type: application/json');
        
        echo json_encode($categoryExists);
    }

    public function editIncomeCategoryAction()
    {
        $incomeCategory = new IncomeCategory($_POST);
        
        if($incomeCategory -> editIncomeCategory()) {

            Income::updateIncomesAssignedToEditedCategory($incomeCategory);

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

    public function deleteIncomeCategoryAction()
    {
        $incomeCategory = new IncomeCategory($_POST);
        
        $incomeCategory -> deleteIncomeCategory();

        Income::deleteIncomesAssignedToDeletedCategory($incomeCategory);

        Flash::addFlashMsg('Your category has been successfully deleted.');
        $this -> redirect('/settings/income-categories');
    }

    public function addIncomeCategoryAction()
    {
        $incomeCategory = new IncomeCategory($_POST);
        
        if($incomeCategory -> addNewIncomeCategory()) {

            Flash::addFlashMsg('Your new category has been successfully added.');
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
