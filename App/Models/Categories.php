<?php

namespace App\Models;

use PDO;

class Categories extends \Core\Model
{
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
        
        $sql = 'SELECT ic.income_category
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

    public static function incomeCategoryExists($categoryName)
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
}
