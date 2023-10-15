<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogMovieGender extends Orm {
    protected $tblName  = 'catalog_movie_gender';

    public function __construct() {
        parent::__construct();
    }


  public function getGendersIn(string $in = null) 
  {
    $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.gender
            FROM
                {$this->tblName}
            WHERE
                {$this->tblName}.catalog_movie_gender_id IN({$in})
            AND 
                {$this->tblName}.status = '1'
            ";

    return $this->connection()->rows($sql);
  }
}