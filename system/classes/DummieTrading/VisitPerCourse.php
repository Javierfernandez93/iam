<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class VisitPerCourse extends Orm {
	protected $tblName = 'visit_per_course';
	public function __construct() {
		parent::__construct();
	}

	public function getCount(int $course_id = null) 
	{
		if(isset($course_id) === true)
		{
			$sql = "SELECT 
						COUNT({$this->tblName}.course_id) as c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.course_id = '{$course_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}
	}


	public function addVisit(int $user_login_id = null,int $course_id = null) : bool
	{
		if($this->exist($user_login_id,$course_id) === false)
		{	
			$VisitPerCourse = new VisitPerCourse;
			$VisitPerCourse->course_id = $course_id;
			$VisitPerCourse->user_login_id = $user_login_id;
			$VisitPerCourse->create_date = time();
			
			return $VisitPerCourse->save();
		}

		return false;
	}

	public function exist(int $user_login_id = null,int $course_id = null) : bool
	{
		if(isset($user_login_id,$course_id) === true)
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
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}
}