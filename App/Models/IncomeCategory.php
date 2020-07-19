<?php

namespace App\Models;

use PDO;

class IncomeCategory extends Category
{
    public static function incomeCategoryIsAssignedToUser($categoryName)
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

        if(self::incomeCategoryIsAssignedToUser($this -> categoryNewName)) {
                
            $this -> validationErrors[] = 'Name already exists.';
        }
        
        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> categoryExistsInIncomeCategories();

            if(!$this -> categoryNewId) {
                
                $this -> saveNewIncomeCategory();
                $this -> categoryNewId = $this -> categoryExistsInIncomeCategories();
            }

            $this -> assignIncomeCategoryToUser();
            $this -> removeIncomeCategoryAssignmentFromUser();

            return true;
        }
        
        return false;
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

    public function saveNewIncomeCategory()
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

    public function deleteIncomeCategory()
    {
        self::removeIncomeCategoryAssignmentFromUser();
    }

    public function addNewIncomeCategory()
    {
        $this -> validateCategoryData();

        if(self::incomeCategoryIsAssignedToUser($this -> categoryNewName)) {
                
            $this -> validationErrors[] = 'Name already exists.';
        }
        
        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> categoryExistsInIncomeCategories();

            if(!$this -> categoryNewId) {
                
                $this -> saveNewIncomeCategory();
                $this -> categoryNewId = $this -> categoryExistsInIncomeCategories();
            }

            $this -> assignIncomeCategoryToUser();

            return true;
        }
        
        return false;
    }
}
