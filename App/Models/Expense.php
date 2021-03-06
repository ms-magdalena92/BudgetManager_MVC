<?php

namespace App\Models;

use PDO;
use \App\Date;

class Expense extends \Core\Model
{
    public $validationErrors = [];
    
    public function __construct($data = [])
    {
        foreach($data as $key => $value) {
            
            $this -> $key = $value;
        };
    }
    
    public function saveExpenseToDB()
    {
        $this -> validateExpenseData();
        
        if(empty($this -> validationErrors)) {
            
            $sql = 'INSERT INTO expenses (user_id, expense_amount, expense_date, payment_method_id, category_id, expense_comment)
                    VALUES (:user_id, :expense_amount, :expense_date,
                    (SELECT payment_method_id FROM payment_methods
                    WHERE payment_method=:payment_method),
                    (SELECT category_id FROM expense_categories
                    WHERE expense_category=:expense_category),
					:expense_comment)';
            
            $db = static::getDBconnection();
            
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
            $stmt -> bindValue(':expense_amount', $this -> amount, PDO::PARAM_STR);
            $stmt -> bindValue(':expense_date', $this -> date, PDO::PARAM_STR);
            $stmt -> bindValue(':payment_method', $this -> payment, PDO::PARAM_STR);
            $stmt -> bindValue(':expense_category', $this -> category, PDO::PARAM_STR);
            $stmt -> bindValue(':expense_comment', $this -> comment, PDO::PARAM_STR);
            
            return $stmt -> execute();
        }
        
        return false;
    }
    
    public function validateExpenseData()
    {
        //Amount validation
        if(isset($this -> amount)) {
            
            if(empty($this -> amount)) {
                
                $this -> validationErrors['amountE1'] = 'Expense amount is required.';
            }
            
            $this -> amount = number_format($this -> amount, 2, '.', '');
            $amount = explode('.', $this -> amount);
            
            if(!is_numeric($this -> amount) || strlen($this -> amount) > 9 || $this -> amount < 0 || !(isset($amount[1]) && strlen($amount[1]) == 2)) {
                
                $this -> validationErrors['amountE2'] = 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.';
            }
        }
        
        //Category validation
        if(!isset($this -> category)) {
            
            $this -> validationErrors['categoryE1'] = 'Expense category is required.';
        }
        
        //Payment method validation
        if(!isset($this -> payment)) {
            
            $this -> validationErrors['categoryE1'] = 'Payment method is required.';
        }
        
        //Date validation
        if(!isset($this -> date)) {
            
            $this -> validationErrors['dateE1'] = 'Date is required.';
        }
        
        //Comment validation
        if(isset($this -> comment)) {
            
            if($this -> comment != '' && !preg_match('/^[A-ZĄĘÓŁŚŻŹĆŃa-ząęółśżźćń 0-9]+$/', $this -> comment)) {
                
                $this -> validationErrors['commentE1'] = 'Comment can contain up to 100 characters - only letters and numbers allowed.';
            }
        }
    }

    public static function updateExpensesAssignedToEditedPaymentMethod($paymentMethod)
    {
        $db = static::getDBconnection();
        
        $sql = 'UPDATE expenses
                SET payment_method_id = :paymentMethodNewId
                WHERE user_id = :loggedUserId AND payment_method_id = :paymentMethodOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':paymentMethodNewId', $paymentMethod -> categoryNewId['payment_method_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':paymentMethodOldId', $paymentMethod -> categoryOldId, PDO::PARAM_INT);
        
        $stmt -> execute();
    }

    public static function deleteExpensesAssignedToDeletedPaymentMethod($paymentMethod)
    {
        $db = static::getDBconnection();
        
        $sql = 'DELETE FROM expenses
                WHERE user_id = :loggedUserId AND payment_method_id = :paymentMethodOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':paymentMethodOldId', $paymentMethod -> categoryOldId, PDO::PARAM_INT);
        
        $stmt -> execute();
    }

    public static function updateExpensesAssignedToEditedCategory($expenseCategory)
    {
        $db = static::getDBconnection();
        
        $sql = 'UPDATE expenses
                SET category_id = :categoryNewId
                WHERE user_id = :loggedUserId AND category_id = :categoryOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':categoryNewId', $expenseCategory -> categoryNewId['category_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':categoryOldId', $expenseCategory -> categoryOldId, PDO::PARAM_INT);
        
        $stmt -> execute();
    }

    public static function deleteExpensesAssignedToDeletedCategory($expenseCategory)
    {
        $db = static::getDBconnection();
        
        $sql = 'DELETE FROM expenses
                WHERE user_id = :loggedUserId AND category_id = :categoryOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':categoryOldId', $expenseCategory -> categoryOldId, PDO::PARAM_INT);
        
        $stmt -> execute();
    }

    public function getMonthlyLimitInfo()
    {
        $startDate = Date::getMonthStartDate($this -> date);
        $endDate = Date::getMonthEndDate($this -> date);
        
        $db = static::getDBconnection();

        $sql = 'SELECT IFNULL(SUM(e.expense_amount), 0) AS expenses_amount, uec.monthly_limit, uec.limit_on
            FROM expenses e NATURAL JOIN user_expense_category uec
            WHERE e.user_id = :loggedUserId
            AND e.category_id = (SELECT category_id FROM expense_categories WHERE expense_category = :category)
            AND uec.category_id =  e.category_id
            AND e.expense_date BETWEEN :startDate AND :endDate';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':category', $this -> category, PDO::PARAM_STR);
        $stmt -> bindValue(':startDate', $startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $endDate, PDO::PARAM_STR);
        
        $stmt -> execute();

        return $stmt -> fetchAll();
    }
}
