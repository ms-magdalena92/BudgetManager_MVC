<?php

namespace App\Models;

use PDO;
use \Core\View;

class User extends \Core\Model
{
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
			
            $this -> $key = $value;
        };
    }

    public function saveUserToDB()
    {
        $password_hash = password_hash($this -> password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO users (username, email, password)
                VALUES (:name, :email, :password_hash)';

        $db = static::getDBconnection();
       
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':name', $this -> userName, PDO::PARAM_STR);
        $stmt -> bindValue(':email', $this -> email, PDO::PARAM_STR);
        $stmt -> bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

        return $stmt -> execute();
    }
}
