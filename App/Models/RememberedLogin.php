<?php

namespace App\Models;

use PDO;
use \App\Token;

class RememberedLogin extends \Core\Model
{
    public static function findUserByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token -> getTokenHash();
        
        $sql = 'SELECT * FROM remembered_logins
                WHERE token_hash = :token_hash';
        
        $db = static::getDBconnection();
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();
        
        return $stmt -> fetch();
    }
    
    public function getRememberedUser()
    {
        return User::findUserByID($this -> user_id);        
    }
    
    public function tokenExpired()
    {
        return strtotime($this -> expiry_date) < time();
    }
    
    public function deleteRememberedLogin()
    {
        $sql = 'DELETE FROM remembered_logins
                WHERE token_hash = :token_hash';
        
        $db = static::getDBconnection();
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':token_hash', $this -> token_hash, PDO::PARAM_STR);
        $stmt -> execute();
    }
}
