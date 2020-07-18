<?php

namespace App\Models;

use PDO;

class Category extends \Core\Model
{
    public $validationErrors = [];
    
    public function __construct($data = [])
    {
        foreach($data as $key => $value) {
            
            $this -> $key = $value;
        };
    }

    public static function assignDefaultCategoriesToNewUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'BEGIN;
                INSERT INTO user_income_category (user_id, category_id)
                SELECT users.user_id, default_income_categories.category_id FROM users, default_income_categories
                WHERE users.user_id = (SELECT max(user_id) FROM users);';
        
        $sql .= 'INSERT INTO user_expense_category (user_id, category_id)
                SELECT users.user_id, default_expense_categories.category_id FROM users, default_expense_categories
                WHERE users.user_id = (SELECT max(user_id) FROM users);';
        
        $sql .= 'INSERT INTO user_payment_method (user_id, payment_method_id)
                SELECT users.user_id, default_payment_methods.payment_method_id FROM users, default_payment_methods
                WHERE users.user_id = (SELECT max(user_id) FROM users);
                COMMIT;';
        
        $stmt = $db -> prepare($sql);
        
        $stmt -> execute();
    }
    
    public static function getCurrentUserIncomeCategories()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT ic.income_category, ic.category_id
                FROM income_categories ic NATURAL JOIN user_income_category uic
                WHERE uic.user_id = :loggedUserId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetchAll();
    }
    
    public static function getCurrentUserExpenseCategories()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT ec.expense_category
                FROM expense_categories ec NATURAL JOIN user_expense_category uec
                WHERE uec.user_id = :loggedUserId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetchAll();
    }
    
    public static function getCurrentUserPaymentMethods()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT pm.payment_method
                FROM payment_methods pm NATURAL JOIN user_payment_method upm
                WHERE upm.user_id = :loggedUserId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetchAll();
    }

    public static function userIncomeCategoryExists($categoryName)
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT *
                FROM user_income_category uic NATURAL JOIN income_categories ic
                WHERE uic.user_id = :loggedUserId AND ic.income_category = :income_category';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':income_category', $categoryName, PDO::PARAM_STR);
        $stmt -> execute();
        
        return $stmt -> fetchAll();
    }

    public function editIncomeCategory()
    {
        $this -> validateCategoryData();
        
        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> categoryExistsInIncomeCategories();

            if(!$this -> categoryNewId) {
                
                $this -> addNewIncomeCategory();
                $this -> categoryNewId = $this -> categoryExistsInIncomeCategories();
            }

            $this -> assignIncomeCategoryToUser();
            $this -> removeIncomeCategoryAssignmentFromUser();

            return true;
        }
        
        return false;
    }
    
    protected function validateCategoryData()
    {
        if(isset($this -> categoryNewName)) {
            
            if($this -> categoryNewName == '') {
                
                $this -> validationErrors[] = 'Name is required.';
            }
            
            if(strlen($this -> categoryNewName) < 2 || strlen($this -> categoryNewName) > 50) {
                
                $this -> validationErrors[] = 'Name needs to be between 2 to 50 characters.';
            }
            
            if(!preg_match('/^[A-ZĄĘÓŁŚŻŹĆŃa-ząęółśżźćń 0-9]+$/', $this -> categoryNewName)) {
                
                $this -> validationErrors[] = 'Name must contain letters and numbers only, special characters not allowed.';
            }

            if(self::userIncomeCategoryExists($this -> categoryNewName)) {
                
                $this -> validationErrors[] = 'Name already exists.';
            }
        }
    }

    protected function categoryExistsInIncomeCategories()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT category_id
                FROM income_categories
                WHERE income_category = :income_category';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':income_category', $this -> categoryNewName, PDO::PARAM_STR);
        $stmt -> execute();
        
        return $stmt -> fetch();
    }

    public function addNewIncomeCategory()
    {
        $db = static::getDBconnection();
        
        $sql = 'INSERT INTO income_categories (income_category)
                VALUES (:categoryName)';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':categoryName', strtolower($this -> categoryNewName), PDO::PARAM_STR);
        $stmt -> execute();
    }

    protected function assignIncomeCategoryToUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'INSERT INTO user_income_category (user_id, category_id)
                VALUES (:loggedUserId, :categoryNewId)';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':categoryNewId', $this -> categoryNewId['category_id'], PDO::PARAM_INT);
        $stmt -> execute();
    }

    protected function removeIncomeCategoryAssignmentFromUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'DELETE FROM user_income_category
                WHERE user_id = :loggedUserId AND category_id = :categoryOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':categoryOldId', $this -> categoryOldId, PDO::PARAM_STR);
        $stmt -> execute();
    }
}
