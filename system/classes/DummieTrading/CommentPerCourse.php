<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class CommentPerCourse extends Orm {
	protected $tblName = 'comment_per_course';
    
	public function __construct() {
        parent::__construct();
	}

    public static function add(array $data = null) : bool
    {
        $CommentPerCourse = new self;   

        if(!$CommentPerCourse->exist($data))
        {
            $CommentPerCourse->comment = $data['comment'];
            $CommentPerCourse->course_id = $data['course_id'];
            $CommentPerCourse->user_login_id = $data['user_login_id'];
            $CommentPerCourse->create_date = time();
            
            return $CommentPerCourse->save();
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

    public function getAll(int $course_id = null) 
    {
        if(isset($course_id) === true)
        {   
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.comment,
                        {$this->tblName}.create_date,
                        {$this->tblName}.status
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.course_id = '{$course_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function hasComment(array $data = null) 
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
}