<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

use DummieTrading\SessionPerCourse;

class Course extends Orm {
	protected $tblName = 'course';

    /* target > 0 package_id */
    const ALL = -1;
    const INACTIVES = 0;
    const ACTIVEs = 1;
    
	public function __construct() {
		parent::__construct();
	}

	public static function setState(array $data = null) : bool
    {
        $Course = new self;
        
        if($Course->loadWhere("course_id = ?", $data['course_id']))
        {
            $Course->status = $data['status'];
            
            return $Course->save();
        }

        return false;
    }

	public static function addCourse(array $data = null)
    {
        $Course = new self;

        if(isset($data['course_id']))
        {
            $Course->loadWhere("course_id = ?",$data['course_id']);
        }

        $Course->title = $data['title'];
        // $Course->user_support_id = $data['user_support_id'];
        $Course->description = isset($data['description']) ? $data['description'] : '';
        $Course->image = isset($data['image']) ? $data['image'] : '';
        $Course->price = isset($data['price']) ? $data['price'] : 0;
        $Course->duration = isset($data['duration']) ? $data['duration'] : '';
        $Course->target = isset($data['target']) ? $data['target'] : self::ALL;
        $Course->catalog_course_type_id = $data['free'] ? 1 : 2;
        
        if($data['free'])
        {
            $Course->price = 0;
        }

        $Course->catalog_course_id = $data['catalog_course_id'];
        $Course->catalog_currency_id = $data['catalog_currency_id'];
        $Course->tag = $data['tag'];
        
        $Course->create_date = time();
    
        if($Course->save())
        {
            foreach($data['sessions'] as $key => $session)
            {
                $session['course_id'] = $Course->getId();
                $session['order'] = $key+1;

                SessionPerCourse::addSession($session);
            }
        }

        return true;
    }

	public function getList()
    {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.title,
                    {$this->tblName}.description,
                    {$this->tblName}.price,
                    {$this->tblName}.create_date,
                    {$this->tblName}.target,
                    {$this->tblName}.image,
                    catalog_course.name,
                    catalog_course_type.catalog_course_type_id,
                    catalog_course_type.type,
                    CONCAT_WS(' ',user_support.names,user_support.last_name,user_support.sur_name) as names
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    catalog_course
                ON
                    catalog_course.catalog_course_id = {$this->tblName}.catalog_course_id
                LEFT JOIN 
                    catalog_course_type
                ON
                    catalog_course_type.catalog_course_type_id = {$this->tblName}.catalog_course_type_id
                LEFT JOIN 
                    user_support
                ON
                    user_support.user_support_id = {$this->tblName}.user_support_id
                WHERE 
                    {$this->tblName}.status = '".Constants::AVIABLE."'
                ";
        
        return $this->connection()->rows($sql);
	}

    public function getFormatted(array $data = null) 
    {
        if(isset($data) === true)
        {
            if($course = $this->get($data['course_id']))
            {
                $CommentPerCourse = new CommentPerCourse;
                $LikePerCourse = new LikePerCourse;

                $course['comments'] = $CommentPerCourse->getAll($data['course_id']);
                $course['hasComment'] = $CommentPerCourse->hasComment($data);
                $course['hasRank'] = $LikePerCourse->hasRank($data);
                
                return $course;
            }
        }

        return false;
	}
    
    public function get($course_id = null) 
    {
        if(isset($course_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.description,
                        {$this->tblName}.price,
                        {$this->tblName}.create_date,
                        {$this->tblName}.image,
                        user_support.image as user_image,
                        catalog_course.name,
                        catalog_course_type.type
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_course
                    ON
                        catalog_course.catalog_course_id = {$this->tblName}.catalog_course_id
                    LEFT JOIN 
                        catalog_course_type
                    ON
                        catalog_course_type.catalog_course_type_id = {$this->tblName}.catalog_course_type_id
                    LEFT JOIN 
                        user_support
                    ON
                        user_support.user_support_id = {$this->tblName}.user_support_id
                    WHERE 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    AND
                        {$this->tblName}.{$this->tblName}_id = '{$course_id}'
                    ";
            
            return $this->connection()->row($sql);
        }

        return false;
	}

    public function getCourses()
    {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.title,
                    {$this->tblName}.description,
                    {$this->tblName}.price,
                    {$this->tblName}.create_date,
                    {$this->tblName}.status,
                    {$this->tblName}.image,
                    catalog_currency.currency,
                    catalog_course.name,
                    catalog_course_type.type,
                    user_support.names
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    catalog_course
                ON
                    catalog_course.catalog_course_id = {$this->tblName}.catalog_course_id
                LEFT JOIN 
                    catalog_course_type
                ON
                    catalog_course_type.catalog_course_type_id = {$this->tblName}.catalog_course_type_id
                LEFT JOIN 
                    user_support
                ON
                    user_support.user_support_id = {$this->tblName}.user_support_id
                LEFT JOIN 
                    catalog_currency
                ON
                    catalog_currency.catalog_currency_id = {$this->tblName}.catalog_currency_id
                WHERE 
                    {$this->tblName}.status = '".Constants::AVIABLE."'
                ";
        
        return $this->connection()->rows($sql);
    }
    
    public function getCourse(int $course_id = null)
    {
        if(isset($course_id))
        {
            $course = $this->_getCourse($course_id);
            $course['free'] = 1;
            $course['tag'] = json_decode($course['tag'],true);
            $course['sessions'] = (new SessionPerCourse)->getList($course_id);
            return $course;
        }

        return false;
    }
    
    public function _getCourse(int $course_id = null)
    {
        if(isset($course_id))
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.description,
                        {$this->tblName}.price,
                        {$this->tblName}.create_date,
                        {$this->tblName}.target,
                        {$this->tblName}.status,
                        {$this->tblName}.tag,
                        {$this->tblName}.image,
                        {$this->tblName}.duration,
                        {$this->tblName}.catalog_course_id,
                        {$this->tblName}.catalog_currency_id,
                        {$this->tblName}.user_support_id,
                        {$this->tblName}.catalog_course_type_id,
                        catalog_currency.currency,
                        catalog_course.name,
                        catalog_course_type.type,
                        user_support.names
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_course
                    ON
                        catalog_course.catalog_course_id = {$this->tblName}.catalog_course_id
                    LEFT JOIN 
                        catalog_course_type
                    ON
                        catalog_course_type.catalog_course_type_id = {$this->tblName}.catalog_course_type_id
                    LEFT JOIN 
                        user_support
                    ON
                        user_support.user_support_id = {$this->tblName}.user_support_id
                    LEFT JOIN 
                        catalog_currency
                    ON
                        catalog_currency.catalog_currency_id = {$this->tblName}.catalog_currency_id
                    WHERE 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    AND 
                        {$this->tblName}.course_id = '{$course_id}'
                    ";
            
            return $this->connection()->row($sql);
        }

        return false;
    }
}
