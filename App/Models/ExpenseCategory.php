<?php

namespace App\Models;

use PDO;

class ExpenseCategory extends Category
{
    public static function expenseCategoryIsAssignedToUser($categoryName, $categoryOldId)
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT *
                FROM user_expense_category uec NATURAL JOIN expense_categories ec
                WHERE uec.user_id = :loggedUserId AND ec.expense_category = :expense_category AND ec.category_id != :categoryOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':expense_category', $categoryName, PDO::PARAM_STR);
        $stmt -> bindValue(':categoryOldId', $categoryOldId, PDO::PARAM_INT);
        $stmt -> execute();
        
        return $stmt -> fetchAll();
    }

    public function editExpenseCategory()
    {
        $this -> validateCategoryData();

        if(self::expenseCategoryIsAssignedToUser($this -> categoryNewName, $this -> categoryOldId)) {

            $this -> validationErrors['name'] = 'Name already exists.';
        }
        
        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> categoryExistsInExpenseCategories();

            if(!$this -> categoryNewId) {
                
                $this -> saveNewExpenseCategory();
                $this -> categoryNewId = $this -> categoryExistsInExpenseCategories();
            }

            if($this -> categoryNewId['category_id'] != $this -> categoryOldId) {

                $this -> assignExpenseCategoryToUser();
                $this -> removeExpenseCategoryAssignmentFromUser();

            } else {
                $this -> updateExpenseCategoryLimit();
            }

            return true;
        }
        
        return false;
    }

    protected function categoryExistsInExpenseCategories()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT category_id
                FROM expense_categories
                WHERE expense_category = :expense_category';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':expense_category', $this -> categoryNewName, PDO::PARAM_STR);
        $stmt -> execute();

        return $stmt -> fetch();
    }

    public function saveNewExpenseCategory()
    {
        $db = static::getDBconnection();
        
        $sql = 'INSERT INTO expense_categories (expense_category)
                VALUES (:categoryName)';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':categoryName', strtolower($this -> categoryNewName), PDO::PARAM_STR);
        $stmt -> execute();
    }

    protected function assignExpenseCategoryToUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'INSERT INTO user_expense_category (user_id, category_id, monthly_limit, limit_on)
                VALUES (:loggedUserId, :categoryNewId, :limitAmount, :monthlyLimit)';

        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':categoryNewId', $this -> categoryNewId['category_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':limitAmount', $this -> limitAmount, PDO::PARAM_INT);
        $stmt -> bindValue(':monthlyLimit', $this -> monthlyLimit, PDO::PARAM_INT);
        $stmt -> execute();
    }
    
    protected function removeExpenseCategoryAssignmentFromUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'DELETE FROM user_expense_category
                WHERE user_id = :loggedUserId AND category_id = :categoryOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':categoryOldId', $this -> categoryOldId, PDO::PARAM_INT);
        $stmt -> execute();
    }

    public function deleteExpenseCategory()
    {
        self::removeExpenseCategoryAssignmentFromUser();
    }

    public function addNewExpenseCategory()
    {
        $this -> validateCategoryData();

        if(!self::expenseCategoryIsAssignedToUser($this -> categoryNewName, $this -> categoryOldId)) {
                
            $this -> validationErrors['name'] = 'Name already exists.';
        }

        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> categoryExistsInExpenseCategories();

            if(!$this -> categoryNewId) {
                
                $this -> saveNewExpenseCategory();
                $this -> categoryNewId = $this -> categoryExistsInExpenseCategories();
            }

            $this -> assignExpenseCategoryToUser();

            return true;
        }
        
        return false;
    }

    protected function updateExpenseCategoryLimit()
    {
        $db = static::getDBconnection();
        
        $sql = 'UPDATE user_expense_category
                SET monthly_limit = :limitAmount, limit_on = :monthlyLimit
                WHERE user_id = :loggedUserId AND category_id = :categoryOldId';

        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':categoryOldId', $this -> categoryOldId, PDO::PARAM_INT);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':limitAmount', $this -> limitAmount, PDO::PARAM_INT);
        $stmt -> bindValue(':monthlyLimit', $this -> monthlyLimit, PDO::PARAM_INT);
        
        $stmt -> execute();
    }
}
