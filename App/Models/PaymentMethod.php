<?php

namespace App\Models;

use PDO;

class PaymentMethod extends Category
{
    public static function paymentMethodIsAssignedToUser($categoryName)
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT *
                FROM user_payment_method upm NATURAL JOIN payment_methods pm
                WHERE upm.user_id = :loggedUserId AND pm.payment_method = :payment_method';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':payment_method', $categoryName, PDO::PARAM_STR);
        $stmt -> execute();
        
        return $stmt -> fetchAll();
    }

    public function editPaymentMethod()
    {
        $this -> validateCategoryData();

        if(self::paymentMethodIsAssignedToUser($this -> categoryNewName)) {
                
            $this -> validationErrors[] = 'Name already exists.';
        }
        
        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> paymentMethodExistsInPaymentMethods();

            if(!$this -> categoryNewId) {
                
                $this -> saveNewPaymentMethod();
                $this -> categoryNewId = $this -> paymentMethodExistsInPaymentMethods();
            }

            $this -> assignPaymentMethodToUser();
            $this -> removePaymentMethodAssignmentFromUser();

            return true;
        }
        
        return false;
    }

    protected function paymentMethodExistsInPaymentMethods()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT payment_method_id
                FROM payment_methods
                WHERE payment_method = :payment_method';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':payment_method', $this -> categoryNewName, PDO::PARAM_STR);
        $stmt -> execute();
        
        return $stmt -> fetch();
    }

    public function saveNewPaymentMethod()
    {
        $db = static::getDBconnection();
        
        $sql = 'INSERT INTO payment_methods (payment_method)
                VALUES (:methodName)';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':methodName', strtolower($this -> categoryNewName), PDO::PARAM_STR);
        $stmt -> execute();
    }

    protected function assignPaymentMethodToUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'INSERT INTO user_payment_method (user_id, payment_method_id)
                VALUES (:loggedUserId, :paymentMethodNewId)';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':paymentMethodNewId', $this -> categoryNewId['payment_method_id'], PDO::PARAM_INT);
        $stmt -> execute();
    }

    protected function removePaymentMethodAssignmentFromUser()
    {
        $db = static::getDBconnection();
        
        $sql = 'DELETE FROM user_payment_method
                WHERE user_id = :loggedUserId AND payment_method_id = :paymentMethodOldId';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':paymentMethodOldId', $this -> categoryOldId, PDO::PARAM_INT);
        $stmt -> execute();
    }

    public function deletePaymentMethod()
    {
        self::removePaymentMethodAssignmentFromUser();
    }

    public function addNewPaymentMethod()
    {
        $this -> validateCategoryData();

        if(self::paymentMethodIsAssignedToUser($this -> categoryNewName)) {
                
            $this -> validationErrors[] = 'Name already exists.';
        }
        
        if(empty($this -> validationErrors)) {
            
            $this -> categoryNewId = $this -> paymentMethodExistsInPaymentMethods();

            if(!$this -> categoryNewId) {
                
                $this -> saveNewPaymentMethod();
                $this -> categoryNewId = $this -> paymentMethodExistsInPaymentMethods();
            }

            $this -> assignPaymentMethodToUser();

            return true;
        }
        
        return false;
    }
}
