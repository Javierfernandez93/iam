<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogInvitationTemplate extends Orm {
  protected $tblName  = 'catalog_invitation_template';
  public function __construct() {
    parent::__construct();
  }

  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.title,
              {$this->tblName}.template,
              {$this->tblName}.create_date
            FROM
              {$this->tblName}
            WHERE
              {$this->tblName}.status = '1'
            ";
    return $this->connection()->rows($sql);
  }
}