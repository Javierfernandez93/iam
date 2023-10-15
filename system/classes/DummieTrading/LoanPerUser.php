<?php

namespace DummieTrading;

use HCStudio\Orm;
use DummieTrading\PaymentPerLoan;

class LoanPerUser extends Orm {
  protected $tblName  = 'loan_per_user';

  public static $REJECTED = -2;
  public static $DELETED = -1;
  public static $PENDING_FOR_APPROVAL = 0;
  public static $FOR_DISBURSEMENT = 1;
  public static $APPROVED = 2;
  public static $FINISHED = 3;

  public static $COMMISSIONABLE_WEEKS = [1,7,14];

  public function __construct() {
    parent::__construct();
  }
  
  public function approveLoan($loan_per_user_id = null) 
  {
    if($this->isPendingForApprove($loan_per_user_id) === true)
    {
      if($this->cargarDonde("loan_per_user_id = ?",$loan_per_user_id))
      {
        $this->status = self::$APPROVED;
        $this->approval_date = time();

        return $this->save();
      }
    }

    return false;
  }

  public function isPendingForApprove($loan_per_user_id = null) 
  {
    if(isset($loan_per_user_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
              AND 
                {$this->tblName}.status = '".self::$PENDING_FOR_APPROVAL."'
              ";

      return $this->connection()->field($sql) ? true : false;
    }
  }

  public function updateAsFinished($loan_per_user_id = null) 
  {
    $LoanPerUser = new LoanPerUser;
    
    if($LoanPerUser->cargarDonde("loan_per_user_id = ?",$loan_per_user_id))
    {
      $LoanPerUser->status = LoanPerUser::$FINISHED;
      
      return $LoanPerUser->save();
    }

    return false;
  }

  public function isLoanFinished($loan_per_user_id = null) 
  {
    if(isset($loan_per_user_id) == true)
    {
      if($loan = $this->getLoanAmmount($loan_per_user_id))
      {
        $PaymentPerLoan = new PaymentPerLoan;
        
        if($ammount_payed = $PaymentPerLoan->getAllPaymentsAmmount($loan_per_user_id))
        {
          return $ammount_payed >= $loan;
        }
      }
    }
  }

  public function getApprobedLoan($user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.create_date,
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              LEFT JOIN 
                catalog_loan 
              ON 
                catalog_loan.catalog_loan_id = {$this->tblName}.catalog_loan_id
              WHERE 
                {$this->tblName}.status = '".self::$APPROVED."'
              ";

      return $this->connection()->row($sql);
    }
  }
  
  public function getCurrentLoan($user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.unique_id,
                {$this->tblName}.create_date,
                {$this->tblName}.user_login_id,
                {$this->tblName}.status
              FROM 
                {$this->tblName}
              LEFT JOIN 
                catalog_loan 
              ON 
                catalog_loan.catalog_loan_id = {$this->tblName}.catalog_loan_id
              WHERE 
                {$this->tblName}.status NOT IN('".self::$DELETED."','".self::$REJECTED."')
              AND 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";

      return $this->connection()->row($sql);
    }
  }

  public function getAll($status = null,$filter = "") 
  {
    if(isset($status) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.create_date,
                client_per_seller.user_support_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.status,
                {$this->tblName}.unique_id,
                {$this->tblName}.commissionable_week,
                {$this->tblName}.approval_date,
                {$this->tblName}.resource_destination,
                {$this->tblName}.authorization_user_support_id,
                catalog_loan.catalog_loan_id,
                catalog_loan.number_of_period,
                catalog_loan.payment,
                catalog_loan.loan,
                catalog_loan.capital,
                LOWER(CONCAT_WS(' ',
                  user_data.names,
                  user_data.last_name,
                  user_data.sur_name
                )) as names
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_data 
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                catalog_loan 
              ON 
                catalog_loan.catalog_loan_id = {$this->tblName}.catalog_loan_id
              LEFT JOIN 
                client_per_seller
              ON 
                client_per_seller.user_login_id = user_data.user_login_id
              WHERE 
                {$this->tblName}.status = '{$status}'
                {$filter}
              ORDER BY 
                {$this->tblName}.create_date
              DESC
              ";

      return $this->connection()->rows($sql);
    }
  }

  public function getLastUniqueId($user_support_id = null) 
  {
    if(isset($user_support_id) === true)
    {
      if($loans = $this->_getLastUniqueId($user_support_id))
      {
        return sizeof($loans)+1;
      }
    }

    return 1;
  }

  public function _getLastUniqueId($user_support_id = null) 
  {
    if(isset($user_support_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                client_per_seller.user_support_id
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client_per_seller 
              ON 
                client_per_seller.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                client_per_seller.user_support_id = '{$user_support_id}'
              AND 
                {$this->tblName}.status IN('".self::$PENDING_FOR_APPROVAL."','".self::$FOR_DISBURSEMENT."','".self::$APPROVED."','".self::$FINISHED."')
              ";

      return $this->connection()->rows($sql);
    }
    
    return false;
  }

  public function getAllLoansBySeller($user_support_id = null,$start_date = null,$end_date = null) 
  {
    if(isset($user_support_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.create_date,
                client_per_seller.user_support_id,
                {$this->tblName}.unique_id,
                {$this->tblName}.commissionable_week,
                {$this->tblName}.user_login_id,
                payment_per_loan.payment_per_loan_id,
                payment_per_loan.status,
                payment_per_loan.period,
                payment_per_loan.ammount
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client_per_seller 
              ON 
                client_per_seller.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                payment_per_loan 
              ON 
                payment_per_loan.loan_per_user_id = {$this->tblName}.loan_per_user_id
              WHERE 
                client_per_seller.user_support_id = '{$user_support_id}'
              AND 
                {$this->tblName}.status = '2'
              AND 
                payment_per_loan.status IN('0','1')
              AND 
                payment_per_loan.verified = '0'
              AND 
                payment_per_loan.create_date
              BETWEEN 
                {$start_date} 
              AND 
                {$end_date} 
              GROUP BY 
                {$this->tblName}.user_login_id
              ORDER BY 
                payment_per_loan.create_date
              DESC 
              ";

      return $this->connection()->rows($sql);
    }
  }

  public function getNextCommissionableWeek($user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      if($last_loan = $this->getLastApprovedLoan($user_login_id))
      {
        $position = array_search($last_loan['commissionable_week'],self::$COMMISSIONABLE_WEEKS);

        return self::$COMMISSIONABLE_WEEKS[$position < sizeof(self::$COMMISSIONABLE_WEEKS) -1 ? $position + 1 : 0];
      } else {
        return self::$COMMISSIONABLE_WEEKS[0];
      }
    }
  }

  public function getLastApprovedLoan(int $user_login_id = null) 
  {
    if($user_support_id = $this->getSellerFromLoanByUserLoginId($user_login_id))
    {
      $sql = "SELECT 
                {$this->tblName}.user_support_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.commissionable_week
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client_per_seller 
              ON 
                client_per_seller.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                client_per_seller.user_support_id = '{$user_support_id}'
              AND 
                client_per_seller.status = '1'
              AND 
                {$this->tblName}.status IN('".self::$APPROVED."','".self::$FOR_DISBURSEMENT."')
              ORDER BY
                {$this->tblName}.create_date
              DESC
              "; 

      return $this->connection()->row($sql);
    }
  }

  public function getSellerFromLoanByUserLoginId(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                client_per_seller.user_support_id
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client_per_seller 
              ON 
                client_per_seller.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                client_per_seller.user_login_id = '{$user_login_id}'
              AND 
                client_per_seller.status = '1'
              AND 
                {$this->tblName}.status IN('".self::$APPROVED."','".self::$FOR_DISBURSEMENT."')
              ";
              
      return $this->connection()->field($sql);
    }
  }

  public function getAllLoansSellers($start_date = null,$end_date = null) 
  {
    if(isset($start_date,$end_date) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.create_date,
                client_per_seller.user_support_id,
                {$this->tblName}.unique_id,
                {$this->tblName}.commissionable_week,
                {$this->tblName}.user_login_id,
                payment_per_loan.payment_per_loan_id,
                payment_per_loan.status,
                payment_per_loan.period,
                payment_per_loan.ammount,
                catalog_loan.capital
              FROM 
                {$this->tblName}
              LEFT JOIN 
                client_per_seller 
              ON 
                client_per_seller.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                payment_per_loan 
              ON 
                payment_per_loan.loan_per_user_id = {$this->tblName}.loan_per_user_id
              LEFT JOIN 
                catalog_loan 
              ON 
                catalog_loan.catalog_loan_id = {$this->tblName}.catalog_loan_id
              WHERE 
                {$this->tblName}.status = '2'
              AND 
                payment_per_loan.status IN('0','1')
              AND 
                payment_per_loan.verified = '0'
              AND 
                payment_per_loan.create_date
              BETWEEN 
                {$start_date} 
              AND 
                {$end_date} 
              GROUP BY 
                {$this->tblName}.user_login_id
              ORDER BY 
                payment_per_loan.create_date
              DESC 
              ";

      return $this->connection()->rows($sql);
    }
  }

  public function getCount() 
  {
    $sql = "SELECT 
              COUNT({$this->tblName}.{$this->tblName}_id) as c
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status IN (".self::$FOR_DISBURSEMENT.",".self::$APPROVED.")
            ";

    return $this->connection()->field($sql);
  }

  public function getLoanAmmount($loan_per_user_id = null) 
  {
    if(isset($loan_per_user_id) === true)
    {
      $sql = "SELECT 
                catalog_loan.loan
              FROM 
                {$this->tblName}
              LEFT JOIN 
                catalog_loan 
              ON 
                catalog_loan.catalog_loan_id = {$this->tblName}.catalog_loan_id
              WHERE 
                {$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
              ";

      return $this->connection()->field($sql);
    }
  }

  public function getLoan($loan_per_user_id = null) 
  {
    if(isset($loan_per_user_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.approval_date as create_date,
                catalog_loan.capital,
                catalog_loan.loan,
                catalog_loan.number_of_period,
                catalog_period.period,
                LOWER(CONCAT_WS(' ',
                  user_data.names,
                  user_data.last_name,
                  user_data.sur_name
                )) as names
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_data 
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN 
                catalog_loan
              ON 
                catalog_loan.catalog_loan_id = {$this->tblName}.catalog_loan_id
              LEFT JOIN 
                catalog_period
              ON 
                catalog_period.catalog_period_id = catalog_loan.catalog_period_id
              WHERE 
                {$this->tblName}.loan_per_user_id = '{$loan_per_user_id}'
              AND 
                {$this->tblName}.status IN ('".self::$APPROVED."','".self::$FOR_DISBURSEMENT."')
              ";

      return $this->connection()->row($sql);
    }
  }

  public function getLoanStatus($status = null) 
  {
    if(isset($status) === true) 
    {
      if($status == self::$DELETED)
      {
        return "Eliminado";
      } else if($status == self::$PENDING_FOR_APPROVAL) {
        return "Pendiente de aprobar";
      } else if($status == self::$APPROVED) {
        return "Aprobado/En curso";
      } else if($status == self::$FOR_DISBURSEMENT) {
        return "Desembolso";
      } else if($status == self::$REJECTED) {
        return "Rechazados";
      } else if($status == self::$FINISHED) {
        return "Finalizado";
      }
    }
  }
}