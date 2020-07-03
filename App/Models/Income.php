<?php

namespace App\Models;

use PDO;

class Income extends \Core\Model
{
    public $validationErrors = [];
    
    public function __construct($data = [])
    {
        foreach($data as $key => $value) {
            
            $this -> $key = $value;
        };
    }
    
    public function saveIncomeToDB()
    {
        $this -> validateIncomeData();
        
        if(empty($this -> validationErrors)) {
            
            $sql = 'INSERT INTO incomes (user_id, income_amount, income_date, category_id, income_comment)
                    VALUES (:user_id, :income_amount, :income_date,
                    (SELECT category_id FROM income_categories
                    WHERE income_category=:income_category),
					:income_comment)';
            
            $db = static::getDBconnection();
            
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
            $stmt -> bindValue(':income_amount', $this -> amount, PDO::PARAM_STR);
            $stmt -> bindValue(':income_date', $this -> date, PDO::PARAM_STR);
            $stmt -> bindValue(':income_category', $this -> category, PDO::PARAM_STR);
            $stmt -> bindValue(':income_comment', $this -> comment, PDO::PARAM_STR);
            
            return $stmt -> execute();
        }
        
        return false;
    }
    
    public function validateIncomeData()
    {
        //Amount validation
        if(isset($this -> amount)) {
            
            if(empty($this -> amount)) {
                
                $this -> validationErrors['amountE1'] = 'Income amount is required.';
            }
            
            $this -> amount = number_format($this -> amount, 2, '.', '');
            $amount = explode('.', $this -> amount);
            
            if(!is_numeric($this -> amount) || strlen($this -> amount) > 9 || $this -> amount < 0 || !(isset($amount[1]) && strlen($amount[1]) == 2)) {
                
                $this -> validationErrors['amountE2'] = 'Enter valid positive amount - maximum 6 integer digits and 2 decimal places.';
            }
        }
        
        //Category validation
        if(!isset($this -> category)) {
            
            $this -> validationErrors['categoryE1'] = 'Income category is required.';
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
