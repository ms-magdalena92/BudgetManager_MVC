<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Token;
use \App\Mail;

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
            
            $token = new Token();
            $hashed_token = $token -> getTokenHash();
            $this -> activation_token = $token -> getTokenValue();
            
            $sql = 'INSERT INTO users (username, email, password, activation_hash_token)
                    VALUES (:name, :email, :password_hash, :activation_token)';
            
            $db = static::getDBconnection();
            
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':name', $this -> userName, PDO::PARAM_STR);
            $stmt -> bindValue(':email', $this -> email, PDO::PARAM_STR);
            $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt -> bindValue(':activation_token', $hashed_token, PDO::PARAM_STR);
            
            return $stmt -> execute();
        }
        
        return false;
    }
    
    public function validateUserData()
    {
        //Name validation
        if(isset($this -> userName)) {
            
            if($this -> userName == '') {
                
                $this -> validationErrors['nameE1'] = 'Name is required.';
            }
            
            if(strlen($this -> userName) < 2 || strlen($this -> userName) > 20) {
                
                $this -> validationErrors['nameE2'] = 'Name needs to be between 2 to 20 characters.';
            }
            
            if(!preg_match('/^[A-Za-z]+$/', $this -> userName)) {
                
                $this -> validationErrors['nameE3'] = 'Name must contain letters only, special characters not allowed.';
            }
        }
        
        //Email validation
        if(isset($this -> email)) {
            
            if(!filter_var($this -> email, FILTER_VALIDATE_EMAIL) || filter_var($this -> email, FILTER_SANITIZE_EMAIL) != $this -> email) {
                
                $this -> validationErrors['emailE1'] = 'Please enter a valid e-mail adress.';
            }
            
            if(static::emailExists($this -> email, $this -> user_id ?? NULL)) {
                
                $this -> validationErrors['emailE1'] = 'An account with this e-mail adress already exists.';
            }
        }
        
        //Password validation
        if(isset($this -> password)) {
            
            if(strlen($this -> password) < 8 || strlen($this -> password) > 50) {
                
                $this -> validationErrors['passwordE1'] = 'Password needs to be between 8 to 50 characters.';
            }
            
            if(!preg_match('/.*[a-z]+.*/i', $this -> password) || !preg_match('/.*\d+.*/i', $this -> password)) {
                
                $this -> validationErrors['passwordE2'] = 'Password must contain at least one letter and at least one number.';
            }
            
            if(isset($this -> passwordConfirm)) {
                
                if($this -> password != $this -> passwordConfirm) {
                    
                    $this -> validationErrors['passwordE3'] = 'Passwords you have entered does not match.';
                }
            }
        }

        if(isset($this -> currentPassword) && $this -> currentPassword == '') {

            $this -> validationErrors['password'] = 'Password is required.';
        }
    }
    
    public static function emailExists($email, $existing_user_id = NULL)
    {
        $user = static::findUserByEmail($email);
        
        if($user) {
            
            if($user -> user_id != $existing_user_id) {
                
            return true;
            }
        }
        
        return false;
    }
    
    public static function findUserByEmail($email)
    {
        $sql = 'SELECT * FROM users
                WHERE email = :email';
        
        $db = static::getDBconnection();
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':email', $email, PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();
        
        return $stmt -> fetch();
    }
    
    public static function authenticate($email, $password)
    {
        $user = static::findUserByEmail($email); 
        
        if ($user) {
            
            if (password_verify($password, $user -> password)) {
                
                return $user;
            }
        }
        
        return false;
    }
    
    public static function findUserByID($id)
    {
        $sql = 'SELECT * FROM users
                WHERE user_id = :id';
        
        $db = static::getDBconnection(); 
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':id', $id, PDO::PARAM_INT);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();
        
        return $stmt -> fetch();
    }
    
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token -> getTokenHash();
        
        $this -> remember_token = $token -> getTokenValue();
        $this -> expiry_timestamp = time()+60*60*24*30;  // 30 days from now
        
        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expiry_date)
                VALUES (:token_hash, :user_id, :expiry_date)';
        
        $db = static::getDBconnection();
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt -> bindValue(':user_id', $this -> user_id, PDO::PARAM_INT);
        $stmt -> bindValue(':expiry_date', date('Y-m-d H:i:s', $this -> expiry_timestamp), PDO::PARAM_STR);
        
        return $stmt -> execute();
    }
    
    public static function requestPasswordReset($email)
    {
        $user = static::findUserByEmail($email);
        
        if($user) {
            
            if($user -> createResetToken()) {
            
                return $user -> sendPasswordResetEmail();
            }
        }
        
        return false;
    }
    
    protected function createResetToken()
    {
        $token = new Token();
        $hashed_token = $token -> getTokenHash();
        $expiry_timestamp = time() + 60*60*2;  // 2 hours from now
        
        $this -> password_reset_token = $token -> getTokenValue();
        
        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash, password_reset_expirity = :expiry_date
                WHERE user_id = :user_id';
        
        $db = static::getDBconnection();
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt -> bindValue(':expiry_date', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt -> bindValue(':user_id', $this -> user_id, PDO::PARAM_INT);
        
        return $stmt -> execute();
    }
    
    protected function sendPasswordResetEmail()
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/password/reset/'.$this -> password_reset_token;
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
        
        $headers = "MIME-Version: 1.0"."\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
        $headers .= 'From: <admin@mybudget.com>'."\r\n";
        
        return Mail::sendEmail($this -> email, 'Password reset', $html, $headers);
    }
    
    public static function findUserByResetToken($token)
    {
        $token = new Token($token);
        $hashed_token = $token -> getTokenHash();
        
        $sql = 'SELECT * FROM users
                WHERE password_reset_hash = :token_hash';
        
        $db = static::getDBconnection();
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt -> setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt -> execute();
        
        $user = $stmt -> fetch();
        
        if($user) {
            
            if(strtotime($user -> password_reset_expirity) > time()) {
                
                return $user;
            }
        }
    }
    
    public function resetPassword($password)
    {
        $this -> password = $password;
        
        $this -> validateUserData();
        
        if (empty($this -> validationErrors)) {
            
            $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);
            
            $sql = 'UPDATE users
                    SET password = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_expirity = NULL
                    WHERE user_id = :user_id';
            
            $db = static::getDBconnection();
            
            $stmt = $db -> prepare($sql);
            $stmt -> bindValue(':user_id', $this -> user_id, PDO::PARAM_INT);
            $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            
            return $stmt -> execute();
        }
        return false;
    }
    
    public function sendActivationEmail()
    {
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/signup/activate/'.$this -> activation_token;
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
        
        $headers = "MIME-Version: 1.0"."\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
        $headers .= 'From: <admin@mybudget.com>'."\r\n";
        
        return Mail::sendEmail($this -> email, 'Activate your account', $html, $headers);
    }
    
    public static function activateAccount($activationToken)
    {
        $token = new Token($activationToken);
        $hashed_token = $token -> getTokenHash();
        
        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash_token = NULL
                WHERE activation_hash_token = :hashed_token';
        
        $db = static::getDBconnection();
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
        
        $stmt -> execute();
    }

    public function editUsername()
    {
        $this -> validateUserData();
            
        $user = static::findUserByID($_SESSION['user_id']);
        
        if ($user) {

            if (password_verify($this -> currentPassword, $user -> password)) {

                if(empty($this -> validationErrors)) {

                    $db = static::getDBconnection();
                    
                    $sql = 'UPDATE users
                            SET username = :newName
                            WHERE user_id = :user_id';

                    $stmt = $db -> prepare($sql);
                    $stmt -> bindValue(':newName', $this -> userName, PDO::PARAM_STR);
                    $stmt -> bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    
                    return $stmt -> execute();
                }

            } else {

                    $this -> validationErrors['password'] = "Password you've entered is wrong. Please try again.";
            }
        }
        
        return false;
    }

    public function editPassword()
    {
        $this -> validateUserData();
            
        $user = static::findUserByID($_SESSION['user_id']);
        
        if ($user) {

            if (password_verify($this -> currentPassword, $user -> password)) {

                if(empty($this -> validationErrors)) {

                    $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);

                    $db = static::getDBconnection();
                    
                    $sql = 'UPDATE users
                            SET password= :newPassword
                            WHERE user_id = :user_id';
                    
                    $stmt = $db -> prepare($sql);
                    $stmt -> bindValue(':newPassword', $password_hash, PDO::PARAM_STR);
                    $stmt -> bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    
                    return $stmt -> execute();
                }

            } else {

                    $this -> validationErrors['password'] = "Password you've entered is wrong. Please try again.";
            }
        }
        
        return false;
    }
}
