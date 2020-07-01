<?php

namespace App;

class Mail
{
	public static function sendEmail($to, $subject, $message, $headers)
	{
		return mail($to, $subject, $message, $headers);
	}
}
