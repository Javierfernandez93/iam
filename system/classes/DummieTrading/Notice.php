<?php

namespace DummieTrading;

use HCStudio\Orm;

class Notice extends Orm {
  protected $tblName  = 'notice';
  const PUBLISHED = 1;
  const UNPUBLISHED = 0;
  const DELETED = -1;

  /* target */
  const ALL = -1;
  const INACTIVES = 0;
  const ACTIVES = 1;
  public function __construct() {
    parent::__construct();
  }
  
  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.title,
              {$this->tblName}.description,
              {$this->tblName}.start_date,
              {$this->tblName}.status,
              {$this->tblName}.button_action,
              {$this->tblName}.end_date,
              {$this->tblName}.catalog_priority_id,
              {$this->tblName}.create_date,
              catalog_notice.catalog_notice_id,
              catalog_notice.notice,
              user_support.names
            FROM 
              {$this->tblName}
            LEFT JOIN
              catalog_notice
            ON 
              catalog_notice.catalog_notice_id = {$this->tblName}.catalog_notice_id
            LEFT JOIN
              user_support
            ON 
              user_support.user_support_id = {$this->tblName}.user_support_id
            WHERE 
              {$this->tblName}.status IN ('".self::PUBLISHED."','".self::UNPUBLISHED."')
            ORDER BY 
              {$this->tblName}.create_date
            DESC
            ";

    return $this->connection()->rows($sql);
  }
  
  public function getAllPublished() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.title,
              {$this->tblName}.description,
              {$this->tblName}.start_date,
              {$this->tblName}.status,
              {$this->tblName}.preview,
              {$this->tblName}.image,
              {$this->tblName}.target,
              {$this->tblName}.button_action,
              {$this->tblName}.modal_class,
              {$this->tblName}.end_date,
              {$this->tblName}.catalog_priority_id,
              {$this->tblName}.create_date,
              catalog_notice.catalog_notice_id,
              catalog_notice.notice,
              user_support.names
            FROM 
              {$this->tblName}
            LEFT JOIN
              catalog_notice
            ON 
              catalog_notice.catalog_notice_id = {$this->tblName}.catalog_notice_id
            LEFT JOIN
              user_support
            ON 
              user_support.user_support_id = {$this->tblName}.user_support_id
            WHERE 
              {$this->tblName}.status IN ('".self::PUBLISHED."')
            ORDER BY 
              {$this->tblName}.create_date
            DESC
            ";

    if($notices = $this->connection()->rows($sql))
    {
      return $notices;
    }
  }
  
  public function getNotice(int $notice_id = null) 
  {
    if(isset($notice_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.title,
                {$this->tblName}.image,
                {$this->tblName}.description,
                {$this->tblName}.catalog_notice_id,
                {$this->tblName}.catalog_priority_id,
                {$this->tblName}.start_date,
                {$this->tblName}.end_date,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.notice_id = '{$notice_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
}