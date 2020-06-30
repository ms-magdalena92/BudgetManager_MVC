<?php

namespace App;

class Flash
{
    const SUCCESS = 'success';
	
    const INFO = 'info';

    const WARNING = 'warning';

    public static function addFlashMsg($message, $type = 'success')
    {
        if(!isset($_SESSION['flash_msg'])) {
			
            $_SESSION['flash_msg'] = [];
        }

        $_SESSION['flash_msg'][] = [
			'body' => $message,
			'type' => $type
		];
    }

    public static function getFlashMsg()
    {
        if (isset($_SESSION['flash_msg'])) {
			
            $messages = $_SESSION['flash_msg'];
            unset($_SESSION['flash_msg']);
            return $messages;
        }
    }
}
