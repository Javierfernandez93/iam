<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    $CatalogPermission = new DummieTrading\CatalogPermission;
    
    if(!$CatalogPermission->exist($data['permission']))
    {
        $CatalogPermission->permission = $data['permission'];
        $CatalogPermission->description = $data['description'];
        $CatalogPermission->create_date = time();
        
        if($CatalogPermission->save())
        {
            $data['s'] = 1;
            $data['r'] = 'DATA_OK';
        } else {
            $data['s'] = 0;
            $data['r'] = 'not_save';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'ALREADY_EXIST';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 