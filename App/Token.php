<?php

namespace App;

class Token
{
    protected $token;
    
    public function __construct($token_value = null)
    {
        if ($token_value) {
            
            $this -> token = $token_value;
            
        } else {
            
            $this -> token = bin2hex(random_bytes(16));
            }
    }
    
    public function getTokenValue()
    {
        return $this -> token;
    }
    
    public function getTokenHash()
    {
        return hash_hmac('sha256', $this -> token, \App\Config::TOKEN_HASH_KEY);
    }
}
