<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class WhatsAppContact extends Orm {
    protected $tblName  = 'whatsapp_contact';

    /* constants */
    const DEFAULT_NAME = 'noname';

    const CARD_PHONE = "@c.u";
    public function __construct() {
        parent::__construct();
    }

    public static function getPhoneFormatted(string $phone = null) : string 
    {
        if (!strstr($phone, self::CARD_PHONE)) {
            return $phone.self::CARD_PHONE;
        }

        return $phone;
    }

    public static function saveContact(string $phone = null,string $name = null) 
    {
        $WhatsAppContact = new WhatsAppContact;
        
        if(!$WhatsAppContact->loadWhere('phone = ?',$phone))
        {
            $WhatsAppContact->phone = $phone;
            $WhatsAppContact->name = isset($name) ? $name : self::DEFAULT_NAME;
            $WhatsAppContact->create_date = time();
            $WhatsAppContact->save();
        }

        return $WhatsAppContact->getId();
    }

    public static function saveContacts(array $contacts = null) 
    {
        if(isset($contacts) == true)
        {
            $contact_ids = [];

            foreach($contacts as $contact)
            {
                $phone = is_array($contact) ? $contact['number'] : $contact;
                $name = is_array($contact) ? $contact['pushname'] : self::DEFAULT_NAME;

                if($id = self::saveContact($phone,$name))
                {
                    $contact_ids[] = $id;
                }
            }

            return $contact_ids;
        }

        return false;
    }
}