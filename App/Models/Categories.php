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
}
