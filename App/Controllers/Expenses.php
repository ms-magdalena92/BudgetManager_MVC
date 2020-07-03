<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Categories;

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
}
