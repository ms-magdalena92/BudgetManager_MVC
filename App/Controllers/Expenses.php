<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;
use \App\Models\Expense;
use \App\Flash;

class Expenses extends Authenticated
{
    protected $expenseCategories;
    protected $paymentMethods;
    
    protected function before()
    {
        parent::before();
        
        $this -> expenseCategories = Categories::getCurrentUserExpenseCategories();
        $this -> paymentMethods = Categories::getCurrentUserPaymentMethods();
    }
    
    public function newAction()
    {
        View::renderTemplate('Expense/new-expense.html', [
            'expenseCategories' => $this -> expenseCategories,
            'paymentMethods' => $this -> paymentMethods
        ]);
    }
    
    public function addExpenseAction()
    {
        $expense = new Expense($_POST);
        
        if($expense -> saveExpenseToDB()) {
            
            Flash::addFlashMsg('Your expense has been successfully added.');
            $this -> redirect('/expenses/new');
            
        } else {
            
            View::renderTemplate('Expense/new-expense.html', [
                'expenseCategories' => $this -> expenseCategories,
                'paymentMethods' => $this -> paymentMethods,
                'expense' => $expense
            ]);
        }
    }
}
