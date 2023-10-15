<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class LikePerCourse extends Orm {
	protected $tblName = 'like_per_course';
    
	public function __construct() {
        parent::__construct();
	}

    public static function add(array $data = null) : bool
    {
        $LikePerCourse = new self;   

        if(!$LikePerCourse->exist($data))
        {
            $LikePerCourse->positive = $data['positive'];
            $LikePerCourse->course_id = $data['course_id'];
            $LikePerCourse->user_login_id = $data['user_login_id'];
            $LikePerCourse->create_date = time();
            
            return $LikePerCourse->save();
        }
        
        return false;
    }

    public function getCount(int $course_id = null) 
    {
        if(isset($course_id) === true)
        {   
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.course_id = '{$course_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }

    public function exist(array $data = null) : bool
    {
        if(isset($data) === true)
        {   
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.course_id = '{$data['course_id']}'
                    AND 
                        {$this->tblName}.user_login_id = '{$data['user_login_id']}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }


    public function hasRank(array $data = null) 
    {
        if(isset($data) === true)
        {   
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.course_id = '{$data['course_id']}'
                    AND 
                        {$this->tblName}.user_login_id = '{$data['user_login_id']}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

}