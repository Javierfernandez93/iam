<?php

namespace DummieTrading;

use HCStudio\Util;
use HCStudio\Orm;

use JFStudio\Constants;

use DummieTrading\CatalogTimezone;

class Conference extends Orm {
  protected $tblName  = 'conference';
    public function __construct() {
    parent::__construct();
  }

  public static function convertConferenceTimeZone(array $conference = null,string $timeZone = null) 
  {
    $time = gmdate("H:i:s", $conference['time']);

    return CatalogTimezone::convertTimeZone(date("Y-m-d {$time}"),$conference['timezone'],$timeZone);
  }

  public static function formatConferences(array $conferences = null,string $timeZone = null) : array
  {
    $_conferences = [];

    foreach(Util::$days as $key => $day) {
      $_conferences[$key] = [
        'day' => $day
      ];

      foreach($conferences as $conference) {
        $schedule = json_decode($conference['schedule'],true);
        
        if(in_array($key,$schedule))
        {
          $conference['time_formatted'] = self::convertConferenceTimeZone($conference,$timeZone);

          $conference['time'] = explode(":",$conference['time_formatted'])[0]*3600;

          $_conferences[$key]['conferences'][] = $conference;
        }
      }
    }

    return $_conferences;
  }

  public function getAll(string $timeZone = null) 
  {
    if($conferences = $this->_getAll())
    {
      return self::formatConferences($conferences,$timeZone);
    }

    return false;
  }

  public function _getAll() {
    $sql = "SELECT 
              {$this->tblName}.title,
              {$this->tblName}.description,
              {$this->tblName}.schedule,
              {$this->tblName}.time,
              {$this->tblName}.link,
              catalog_conference.title as catalog_conference_title,
              catalog_speaker.image,
              LOWER(CONCAT_WS(' ',catalog_speaker.name,catalog_speaker.last_name)) as name,
              catalog_timezone.timezone
            FROM 
              {$this->tblName}
            LEFT JOIN 
              catalog_conference
            ON 
              catalog_conference.catalog_conference_id = {$this->tblName}.catalog_conference_id
            LEFT JOIN 
              catalog_speaker
            ON 
              catalog_speaker.catalog_speaker_id = {$this->tblName}.catalog_speaker_id
            LEFT JOIN 
              catalog_timezone
            ON 
              catalog_timezone.catalog_timezone_id = {$this->tblName}.catalog_timezone_id
            WHERE
              {$this->tblName}.status = '".Constants::AVIABLE."'
            ";

    return $this->connection()->rows($sql);
  }
}
