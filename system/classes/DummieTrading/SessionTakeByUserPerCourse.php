<?php

namespace DummieTrading;

use HCStudio\Orm;

class SessionTakeByUserPerCourse extends Orm {
	protected $tblName = 'session_take_by_user_per_course';
	public static $TEXT = 1;
	public static $VIDEO = 2;
	public static $AUDIO = 3;
	public function __construct() {
		parent::__construct();
	}

    public static function setSessionAsTaked(int $session_per_course_id = null,int $user_login_id = null) 
    {
        $SessionTakeByUserPerCourse = new self;
        $SessionTakeByUserPerCourse->session_per_course_id = $session_per_course_id;
        $SessionTakeByUserPerCourse->user_login_id = $user_login_id;
        $SessionTakeByUserPerCourse->create_date = time();
    
        if($SessionTakeByUserPerCourse->save())
        {
            return $SessionTakeByUserPerCourse->data();
        }

        return false;
    }

    public function hasLessonTaked(int $course_id = null,int $user_login_id = null) : bool
    {
        if(isset($course_id,$user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        session_per_course
                    ON 
                        session_per_course.session_per_course_id = {$this->tblName}.session_per_course_id
                    LEFT JOIN 
                        course
                    ON 
                        course.course_id = session_per_course.course_id
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND
                        course.course_id = '{$course_id}'
                    AND
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->rows($sql) ? true : false;
        }

        return false;
	}

    public function getLastSessionTaked(int $course_id = null,int $user_login_id = null) 
    {
        if(isset($course_id,$user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        session_per_course.title
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        session_per_course
                    ON 
                        session_per_course.session_per_course_id = {$this->tblName}.session_per_course_id
                    LEFT JOIN 
                        course
                    ON 
                        course.course_id = session_per_course.course_id
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND
                        course.course_id = '{$course_id}'
                    AND
                        {$this->tblName}.status = '1'
                    ORDER BY 
                        {$this->tblName}.create_date
                    DESC 
                    ";

            return $this->connection()->row($sql);
        }

        return false;
    }

    public function getSessionInfo(int $session_per_course_id = null,int $user_login_id = null) 
    {
        if(isset($session_per_course_id,$user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.create_date,
                        {$this->tblName}.comment
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.session_per_course_id = '{$session_per_course_id}'
                    AND
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->row($sql);
        }

        return false;
    }

    public function isSessionTaked(int $session_per_course_id = null,int $user_login_id = null) : bool 
    {
        if(isset($session_per_course_id,$user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.session_per_course_id = '{$session_per_course_id}'
                    AND
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

    public function isAviableCourse(int $session_take_by_user_per_course_id = null,int $user_login_id = null) : bool
    {
        if(isset($session_take_by_user_per_course_id,$user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id 
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.session_take_by_user_per_course_id = '{$session_take_by_user_per_course_id}'
                    AND
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

    public function getSessionPerCourseId(int $session_take_by_user_per_course_id = null,int $user_login_id = null) 
    {
        if(isset($session_take_by_user_per_course_id,$user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.session_per_course_id,
                        session_per_course.order,
                        session_per_course.course_id,
                        course.title
                    FROM 
                        {$this->tblName}
                    LEFT JOIN
                        session_per_course 
                    ON 
                        session_per_course.session_per_course_id = {$this->tblName}.session_per_course_id
                    LEFT JOIN
                        course 
                    ON 
                        course.course_id = session_per_course.course_id
                    WHERE 
                        {$this->tblName}.session_take_by_user_per_course_id = '{$session_take_by_user_per_course_id}'
                    AND
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->row($sql);
        }

        return false;
    }
}