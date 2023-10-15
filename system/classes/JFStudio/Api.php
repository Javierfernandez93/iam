<?php

namespace JFStudio;

use JFStudio\ApiModelErrors; 
use JFStudio\SimpleCipher; 
use HCStudio\Util;

class Api {
    private $logged = false; 
    private $company_id = false; 
    public function __construct() {

        if($this->hasParamsForLogin() === true)
        {
            $params = [
                'token' => str_replace(" ", "+", Util::getVarFromPGS("token")),
                'key' => Util::getVarFromPGS("secret")
            ];

            if($company_id = SimpleCipher::decrypt($params))
            {
                $this->setCompanyId($company_id);
                $this->setLogged(true);
            } else {
                $this->setLogged(false);
            }
        }
    }
    public function hasParamsForLogin() 
    {
        if(Util::getVarFromPGS("token"))
        {
            if(Util::getVarFromPGS("secret"))
            {
                return true;
            }
        }

        return false;
    }
    public function setLogged($logged = null) {
        $this->logged = $logged;
    }
    public function getLogged() {
        return $this->logged;
    }
    public function setCompanyId($company_id = null) {
        $this->company_id = $company_id;
    }
    public function getCompanyId() {
        return $this->company_id;
    }
}