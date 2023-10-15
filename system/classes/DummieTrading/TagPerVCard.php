<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Connection;

use DummieTrading\CatalogTagValue;
use World\Country;

class TagPerVCard extends Orm {
    protected $tblName  = 'tag_per_vcard';
    public function __construct() {
        parent::__construct();
    }

    public function _getAll(int $vcard_per_user_id = null) 
    {
        if (isset($vcard_per_user_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.value,
                        catalog_tag_template.tag,
                        catalog_tag_value.catalog_tag_value_id
                    FROM
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_tag_template
                    ON 
                        catalog_tag_template.catalog_tag_template_id = {$this->tblName}.catalog_tag_template_id
                    LEFT JOIN 
                        catalog_tag_value
                    ON 
                        catalog_tag_value.catalog_tag_value_id = catalog_tag_template.catalog_tag_value_id
                    WHERE
                        {$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
                    AND 
                        catalog_tag_template.status = '1'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getValue($vcard_per_user_id = null,$catalog_tag_template_id = null) 
    {
        if (isset($vcard_per_user_id,$catalog_tag_template_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.value
                    FROM
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
                    AND 
                        {$this->tblName}.catalog_tag_template_id = '{$catalog_tag_template_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }

    public function getValueByTag(int $vcard_per_user_id = null,string $tag = null) 
    {
        if (isset($vcard_per_user_id,$tag) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.value
                    FROM
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_tag_template
                    ON 
                        catalog_tag_template.catalog_tag_template_id = {$this->tblName}.catalog_tag_template_id
                    WHERE
                        {$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
                    AND 
                        catalog_tag_template.tag = '{$tag}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }

    public static function formatValue(array $catalog_tag_template = null) : string
    {
        if($catalog_tag_template['catalog_tag_value_id'] == CatalogTagValue::PHONE)
        {
            $catalog_tag_template['value'] = json_encode([
                'phone_code' => (new Country)->getPhoneCodeByCountryId($catalog_tag_template['value']['country_id']),
                'country_id' => $catalog_tag_template['value']['country_id'],
                'phone' => $catalog_tag_template['value']['phone']
            ]);
        }
    
        return $catalog_tag_template['value'];
    }
    
    public static function unFormatValue(array $catalog_tag_template = null) 
    {
        if($catalog_tag_template['catalog_tag_value_id'] == CatalogTagValue::PHONE)
        {
            if($catalog_tag_template['value'])
            {
                $catalog_tag_template['value'] = json_decode($catalog_tag_template['value'],true);
            } else {
                $catalog_tag_template['value'] = [
                    'country_id' => 159,
                    'phone_code' => '',
                    'phone' => '',
                ];
            }
        }
    
        return $catalog_tag_template['value'];
    }
    
    public static function unFormatURLImage(string $value = null) 
    {
        if($value)
        {
            return str_replace("../../", Connection::getMainPath()."/", $value);
        }
    }

    public static function unFormatPhone(string $value = null) 
    {
        if($value)
        {
            $value = json_decode($value,true);

            return ((new Country)->getPhoneCodeByCountryId($value['country_id'])) . $value['phone'];
        }
    }
    
    public static function unFormatValueFull(array $catalog_tag_template = null) 
    {
        if($catalog_tag_template['catalog_tag_value_id'] == CatalogTagValue::PHONE)
        {   
            $catalog_tag_template['value'] = self::unFormatPhone($catalog_tag_template['value']);
        } if($catalog_tag_template['catalog_tag_value_id'] == CatalogTagValue::IMAGE) {   
            $catalog_tag_template['value'] = self::unFormatURLImage($catalog_tag_template['value']);
        }
    
        return $catalog_tag_template['value'];
    }

    public function getAll(int $vcard_per_user_id = null) 
    {
        if (isset($vcard_per_user_id) === true) 
        {
            if($tags = $this->_getAll($vcard_per_user_id))
            {
                foreach($tags as $tag)
                {
                    $tag['value'] = self::unFormatValueFull($tag);

                    $_tags[$tag['tag']] = $tag['value'];
                }
            }

            return $_tags;
        }

        return false;
    }
}