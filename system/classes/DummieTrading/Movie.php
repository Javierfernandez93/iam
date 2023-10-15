<?php

namespace DummieTrading;

use HCStudio\Orm;
use DummieTrading\CatalogMovieGender;

class Movie extends Orm {
  protected $tblName  = 'movie';
  
  public function __construct() {
    parent::__construct();
  }
  
  public static function getFilter(array $filter = null) 
  {
    if($filter['catalog_movie_gender_id'])
    {
      $ids = implode(",",$filter['catalog_movie_gender_id']);
      return " AND movie.catalog_movie_gender_id LIKE '%{$ids}%'";
    }
  }

  public function _getAll(string $filter = null) 
  {
    if($movies = $this->getAll($filter))
    {
      $CatalogMovieGender = new CatalogMovieGender;

      return array_map(function($movie) use($CatalogMovieGender){
        if($movie['catalog_movie_gender_id'])
        {
          $movie['catalog_movie_gender_id'] = json_decode($movie['catalog_movie_gender_id'],true);
          $movie['genders'] = $CatalogMovieGender->getGendersIn(implode(",",$movie['catalog_movie_gender_id']));
        } else {
          $movie['genders'] = false;
        }

        return $movie;
      },$movies);
    }
  }
  
  public function getAll(string $filter = null) 
  {
    $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.title,
                {$this->tblName}.year,
                {$this->tblName}.image,
                {$this->tblName}.catalog_movie_gender_id,
                {$this->tblName}.description,
                {$this->tblName}.link
            FROM
                {$this->tblName}
            WHERE
                {$this->tblName}.status = '1'
                {$filter}
            ";

    return $this->connection()->rows($sql);
  }
  
  public function get(int $movie_id = null) 
  {
    if(isset($movie_id) === true)
    {
      $sql = "SELECT 
                  {$this->tblName}.{$this->tblName}_id,
                  {$this->tblName}.title,
                  {$this->tblName}.year,
                  {$this->tblName}.image,
                  {$this->tblName}.description,
                  {$this->tblName}.link
              FROM
                  {$this->tblName}
              WHERE
                  {$this->tblName}.movie_id = '{$movie_id}'
              AND 
                  {$this->tblName}.status = '1'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
}