<?php

namespace App\Models;

use PDO;
use \App\Date;

class Balance extends \Core\Model
{
    public function __construct($data = [])
    {
        foreach($data as $key => $value) {
            
            $this -> $key = $value;
        };
    }
    
    public function getCurrentMonthData()
    {
        $this -> startDate = Date::getCurrentMonthStartDate();
        $this -> endDate = Date::getCurrentMonthEndDate();
        
        $this -> getBalanceData();
    }
    
    public function getLastMonthData()
    {
        $this -> startDate = Date::getLastMonthStartDate();
        $this -> endDate = Date::getLastMonthEndDate();
        
        $this -> getBalanceData();
    }
    
    public function getCurrentYearData()
    {
        $this -> startDate = Date::getCurrentYearStartDate();
        $this -> endDate = Date::getCurrentYearEndDate();
        
        $this -> getBalanceData();
    }
    
    protected function getBalanceData()
    {
        $this -> getGroupedIncomes();
        $this -> getAllIncomes();
        $this -> countTotalIncome();
        
        $this -> getGroupedExpenses();
        $this -> getAllExpenses();
        $this -> countTotalExpense();
    }
    
    protected function getGroupedIncomes()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT i.category_id, ic.income_category, SUM(i.income_amount) AS income_amount
                FROM incomes i NATURAL JOIN income_categories ic
                WHERE i.user_id=:loggedUserId AND i.income_date BETWEEN :startDate AND :endDate
                GROUP BY i.category_id
                ORDER BY income_amount DESC';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $this -> startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $this -> endDate, PDO::PARAM_STR);
        $stmt -> execute();
        
        $this -> groupedIncomes = $stmt -> fetchAll();
    }
    
    protected function getGroupedExpenses()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT e.category_id, ec.expense_category, SUM(e.expense_amount) AS expense_amount
                FROM expenses e NATURAL JOIN expense_categories ec
                WHERE e.user_id=:loggedUserId AND e.expense_date BETWEEN :startDate AND :endDate
                GROUP BY e.category_id
                ORDER BY expense_amount DESC';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $this -> startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $this -> endDate, PDO::PARAM_STR);
        $stmt -> execute();
        
        $this -> groupedExpenses = $stmt -> fetchAll();
    }
    
    protected function getAllIncomes()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT category_id, income_date, income_amount, income_comment
                FROM incomes
                WHERE user_id=:loggedUserId AND income_date BETWEEN :startDate AND :endDate
                ORDER BY income_date ASC, category_id';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $this -> startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $this -> endDate, PDO::PARAM_STR);
        $stmt -> execute();
        
        $this -> detailedIncomes = $stmt -> fetchAll();
    }
    
    protected function getAllExpenses()
    {
        $db = static::getDBconnection();
        
        $sql = 'SELECT category_id, e.expense_date, e.expense_amount, pm.payment_method, e.expense_comment
                FROM expenses e NATURAL JOIN payment_methods pm
                WHERE e.user_id=:loggedUserId AND e.expense_date BETWEEN :startDate AND :endDate
                ORDER BY e.expense_date ASC, category_id';
        
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':loggedUserId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt -> bindValue(':startDate', $this -> startDate, PDO::PARAM_STR);
        $stmt -> bindValue(':endDate', $this -> endDate, PDO::PARAM_STR);
        $stmt -> execute();
        
        $this -> detailedExpenses = $stmt -> fetchAll();
    }
    
    protected function countTotalIncome()
    {
        $this -> totalIncome = 0;
        
        if(!empty($this -> groupedIncomes)) {
            
            foreach ($this -> groupedIncomes as $income) {
                
                $this -> totalIncome += $income['income_amount'];
            }
        }
    }
    
    protected function countTotalExpense()
    {
        $this -> totalExpense = 0;
        
        if(!empty($this -> groupedExpenses)) {
            
            foreach ($this -> groupedExpenses as $expense) {
                
                $this -> totalExpense += $expense['expense_amount'];
            }
        }
    }
}
