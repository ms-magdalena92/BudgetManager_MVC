<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance;

class ShowBalance extends Authenticated
{
    public function currentMonthAction()
    {
        $currentMonth = new Balance();
        
        $currentMonth -> getCurrentMonthData();
        
        View::renderTemplate('Balance/show-balance.html', ['balance' => $currentMonth]);
    }
    
    public function lastMonthAction()
    {
        $lastMonth = new Balance();
        
        $lastMonth -> getLastMonthData();
        
        View::renderTemplate('Balance/show-balance.html', ['balance' => $lastMonth]);
    }
    
    public function currentYearAction()
    {
        $currentYear = new Balance();
        
        $currentYear -> getCurrentYearData();
        
        View::renderTemplate('Balance/show-balance.html', ['balance' => $currentYear]);
    }
    
    public function customPeriodAction()
    {
        $customPeriod = new Balance($_POST);
        
        $customPeriod -> getcustomPeriodData();
        
        View::renderTemplate('Balance/show-balance.html', ['balance' => $customPeriod]);
    }
}
