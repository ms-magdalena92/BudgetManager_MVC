<?php

namespace App;

class Date
{
    public static function getCurrentDate()
    {
        return date("Y-m-d");
    }
    
    public static function getCurrentMonthStartDate()
    {
        return date('Y-m-01');
    }
    
    public static function getCurrentMonthEndDate()
    {
        return date('Y-m-t');
    }
}
