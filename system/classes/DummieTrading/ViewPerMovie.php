<?php

namespace DummieTrading;

use HCStudio\Orm;

class ViewPerMovie extends Orm {
  protected $tblName  = 'view_per_movie';
  
  public function __construct() {
    parent::__construct();
  }
  
  public static function add(int $user_login_id = null,int $movie_id = null) : bool
  {
    $ViewPerMovie = new ViewPerMovie;
    $ViewPerMovie->user_login_id = $user_login_id;
    $ViewPerMovie->movie_id = $movie_id;
    $ViewPerMovie->create_date = time();
        
    return $ViewPerMovie->save();
  }
}