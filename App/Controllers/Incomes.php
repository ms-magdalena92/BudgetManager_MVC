<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Models\Income;
use \App\Flash;

class Incomes extends Authenticated
{
    protected $incomeCategories;
    
    protected function before()
    {
        parent::before();
        
        $this -> incomeCategories = Category::getCurrentUserIncomeCategories();
    }
    
    public function newAction()
    {
        View::renderTemplate('Income/new-income.html', [
            'incomeCategories' => $this -> incomeCategories
        ]);
    }
    
    public function addIncomeAction()
    {
        $income = new Income($_POST);
        
        if($income -> saveIncomeToDB()) {
            
            Flash::addFlashMsg('Your income has been successfully added.');
            $this -> redirect('/incomes/new');
            
        } else {
            
            View::renderTemplate('Income/new-income.html', [
                'incomeCategories' => $this -> incomeCategories,
                'income' => $income
            ]);
        }
    }
}
