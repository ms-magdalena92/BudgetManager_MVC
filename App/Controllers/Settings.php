<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Category;
use \App\Models\IncomeCategory;
use \App\Models\ExpenseCategory;
use \App\Models\PaymentMethod;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\User;
use \App\Flash;

class Settings extends Authenticated
{
    protected static function getIncomeCategories()
    {
        return Category::getCurrentUserIncomeCategories();
    }

    protected static function getPaymentMethods()
    {
        return Category::getCurrentUserPaymentMethods();
    }

    protected static function getExpenseCategories()
    {
        return Category::getCurrentUserExpenseCategories();
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

        if(isset($_POST['categoryType']) &&  $_POST['categoryType'] == 'payment_method') {
            
            $categoryExists = !PaymentMethod::paymentMethodIsAssignedToUser($_POST['categoryNewName']);
        }

        if(isset($_POST['categoryType']) &&  $_POST['categoryType'] == 'expense') {
            
            $categoryExists = !ExpenseCategory::expenseCategoryIsAssignedToUser($_POST['categoryNewName'], $_POST['categoryOldId']);
        }

        header('Content-Type: application/json; charset=utf-8');
        
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

    public function expenseCategoriesAction()
    {
        $expenseCategories = self::getExpenseCategories();

        View::renderTemplate('Settings/expense-categories.html', [
            'expenseCategories' => $expenseCategories
        ]);
    }

    public function editExpenseCategoryAction()
    {
        $expenseCategory = new ExpenseCategory($_POST);
        
        if($expenseCategory -> editExpenseCategory()) {

            expense::updateExpensesAssignedToEditedCategory($expenseCategory);

            Flash::addFlashMsg('Your category has been successfully edited.');
            $this -> redirect('/settings/expense-categories');
            
        } else {
            
            $expenseCategories = self::getExpenseCategories();

            View::renderTemplate('Settings/expense-categories.html', [
                'expenseCategories' => $expenseCategories,
                'expenseCategory' => $expenseCategory
            ]);
        }
    }

    public function deleteExpenseCategoryAction()
    {
        $expenseCategory = new ExpenseCategory($_POST);
        
        $expenseCategory -> deleteExpenseCategory();

        Expense::deleteExpensesAssignedToDeletedCategory($expenseCategory);

        Flash::addFlashMsg('Your category has been successfully deleted.');
        $this -> redirect('/settings/expense-categories');
    }

    public function addExpenseCategoryAction()
    {
        $expenseCategory = new ExpenseCategory($_POST);
        
        if($expenseCategory -> addNewExpenseCategory()) {

            Flash::addFlashMsg('Your new category has been successfully added.');
            $this -> redirect('/settings/expense-categories');
            
        } else {
            
            $expenseCategories = self::getExpenseCategories();

            View::renderTemplate('Settings/expense-categories.html', [
                'expenseCategories' => $expenseCategories,
                'expenseCategory' => $expenseCategory
            ]);
        }
    }

    public function paymentMethodsAction()
    {
        $paymentMethods = self::getPaymentMethods();

        View::renderTemplate('Settings/payment-methods.html', [
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function editPaymentMethodAction()
    {
        $paymentMethod = new PaymentMethod($_POST);
        
        if($paymentMethod -> editPaymentMethod()) {

            Expense::updateExpensesAssignedToEditedPaymentMethod($paymentMethod);

            Flash::addFlashMsg('Your payment method has been successfully edited.');
            $this -> redirect('/settings/payment-methods');
            
        } else {
            
            $paymentMethods = self::getpaymentMethods();

            View::renderTemplate('Settings/payment-methods.html', [
                'paymentMethods' => $paymentMethods,
                'paymentMethod' => $paymentMethod
            ]);
        }
    }

    public function deletePaymentMethodAction()
    {
        $paymentMethod = new PaymentMethod($_POST);
        
        $paymentMethod -> deletePaymentMethod();

        Expense::deleteExpensesAssignedToDeletedPaymentMethod($paymentMethod);

        Flash::addFlashMsg('Your payment method has been successfully deleted.');
        $this -> redirect('/settings/payment-methods');
    }

    public function addPaymentMethodAction()
    {
        $paymentMethod = new paymentMethod($_POST);
        
        if($paymentMethod -> addNewPaymentMethod()) {

            Flash::addFlashMsg('Your payment method has been successfully added.');
            $this -> redirect('/settings/payment-methods');
            
        } else {
            
            $paymentMethod = self::getPaymentMethods();

            View::renderTemplate('Settings/payment-methods.html', [
                'paymentMethods' => $paymentMethods,
                'paymentMethod' => $paymentMethod
            ]);
        }
    }

    public function editUsernameAction()
    {
        $user = new User($_POST);
        
        if($user -> editUsername()) {

            Flash::addFlashMsg('Name has been successfully edited.');
            $this -> redirect('/settings/profile');
            
        } else {
            
            View::renderTemplate('Settings/profile.html', ['user' => $user]);
        }
    }
}
