<?php

namespace App\Models;

use PDO;
use \Core\View;

class User extends \Core\Model
{
    public $validationErrors = [];
	
	public function __construct($data = [])
    {
        foreach($data as $key => $value) {
			
            $this -> $key = $value;
        };
    }

    public function saveUserToDB()
    {
        $this -> validateUserData();
		
		if(empty($this -> validationErrors)) {
		    
		    $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (username, email, password)
                    VALUES (:name, :email, :password_hash)';

            $db = static::getDBconnection();
       
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':name', $this -> userName, PDO::PARAM_STR);
            $stmt -> bindValue(':email', $this -> email, PDO::PARAM_STR);
            $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

			return $stmt -> execute();
		
		} else {
		    
		    return false;
		}
	}
	
	public function validateUserData()
    {
		//Name validation
		if($this -> userName == '') {
			
            $this -> validationErrors['nameE1'] = 'Name is required.';
        }
		
		if(strlen($this -> userName) < 2 || strlen($this -> userName) > 20) {
			
            $this -> validationErrors['nameE2'] = 'Name needs to be between 2 to 20 characters.';
        }
		
		if(!preg_match('/[a-z]+/i', $this -> userName)) {
			
            $this -> validationErrors['nameE3'] = 'Name must contain letters only, special characters not allowed.';
        }
		
		//Email validation
		if(!filter_var($this -> email, FILTER_VALIDATE_EMAIL) || filter_var($this -> email, FILTER_SANITIZE_EMAIL) != $this -> email) {
			
            $this -> validationErrors['emailE1'] = 'Please enter a valid e-mail adress.';
        }
		
        if(static::emailExists($this -> email)) {
			
            $this -> validationErrors['emailE1'] = 'An account with this e-mail adress already exists.';
        }
		
		//Password validation
		if(isset($this->password)) {

            if(strlen($this -> password) < 8 || strlen($this -> password) > 50) {
                
				$this -> validationErrors['passwordE1'] = 'Password needs to be between 8 to 50 characters.';
            }

            if(!preg_match('/.*[a-z]+.*/i', $this -> password) || !preg_match('/.*\d+.*/i', $this -> password)) {
                
				$this -> validationErrors['passwordE2'] = 'Password must contain at least one letter and at least one number.';
            }
			
			if($this -> password != $this -> passwordConfirm) {
			
				$this -> validationErrors['passwordE3'] = 'Passwords you have entered does not match.';
			}
        }
    }

    public static function emailExists($email)
    {
		$user = static::findUserByEmail($email);
		
        if($user) {

            return true;
        }
		
        return false;
    }
	
    public static function  findUserByEmail($email)
    {
        $sql = 'SELECT * 
				FROM users
				WHERE email = :email';

        $db = static::getDBconnection();
		
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':email', $email, PDO::PARAM_STR);
        $stmt -> execute();

        return $stmt -> fetch() !== false;
    }
}
