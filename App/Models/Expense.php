<?php

namespace App\Models;

use PDO;

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
                    (SELECT category_id FROM expense_categories
                    WHERE expense_category=:expense_category),
                    (SELECT payment_method_id FROM payment_methods
                    WHERE payment_method=:payment_method),
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
}
