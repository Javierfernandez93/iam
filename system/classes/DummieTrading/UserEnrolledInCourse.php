<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserEnrolledInCourse extends Orm {
	protected $tblName = 'user_enrolled_in_course';
	public function __construct() {
		parent::__construct();
	}

	public static function enrollInCourse(int $course_id = null,int $user_login_id = null) : bool
    {
        $UserEnrolledInCourse = new UserEnrolledInCourse;
        
        $UserEnrolledInCourse->user_login_id = $user_login_id;
        $UserEnrolledInCourse->course_id = $course_id;
        $UserEnrolledInCourse->create_date = time();

        return $UserEnrolledInCourse->save();
    }

	public function isEnrolled(int $course_id = null,int $user_login_id = null) : bool
    {
        if (isset($course_id,$user_login_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.course_id = '{$course_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
            return $this->connection()->field($sql) ? true : false;
        }

        return false;
	}

    public function getMyCourses($user_login_id = null)
    {
        if (isset($user_login_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        course.course_id,
                        course.title
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        course
                    ON
                        {$this->tblName}.course_id = course.course_id
                        
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
            
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getCoutEnrolledCourses($user_login_id = null) 
    {
        if (isset($user_login_id) === true) 
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
            
            return $this->connection()->field($sql);
        }

        return false;
	}

    public function countUsersInCourse($course_id = null) 
    {
        if (isset($course_id) === true) 
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.course_id = '{$course_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
            
            return $this->connection()->field($sql);
        }

        return false;
    }
}