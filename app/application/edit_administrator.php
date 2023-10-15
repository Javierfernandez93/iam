<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['administrator']['user_support_id'])
    {
        $UserSupportUpdate = new DummieTrading\UserSupport(false,false);
        
        if($UserSupportUpdate->cargarDonde('user_support_id = ?',$data['administrator']['user_support_id']))
        {
            $UserSupportUpdate->names = ucwords(strtolower($data['administrator']['names']));
            $UserSupportUpdate->password = $data['administrator']['password'] ? sha1($data['administrator']['password']) : $UserSupportUpdate->password;
            $UserSupportUpdate->email = strtolower($data['administrator']['email']);
            
            if($UserSupportUpdate->save())
            {
                if(sizeof($data['administrator']['permissions']) > 0)
                {
                    if(savePermissions($UserSupportUpdate->getId(),$data['administrator']['permissions']))
                    {
                        $data['permissions_saved'] = true;
                    }
                }
                $data['s'] = 1;
                $data['r'] = 'SAVE_OK';   
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_SAVE';   
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_USER_SUPPORT';   
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_USER_SUPPORT_ID';    
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function savePermissions(int $user_support_id = null,array $permissions = null) : bool
{
    foreach ($permissions as $permission)
    {
        $PermissionPerUserSupport = new DummieTrading\PermissionPerUserSupport;
        $PermissionPerUserSupport->cargarDonde("user_support_id = ? AND catalog_permission_id = ?",[$user_support_id,$permission['catalog_permission_id']]);
        $PermissionPerUserSupport->user_support_id = $user_support_id;
        $PermissionPerUserSupport->catalog_permission_id = $permission['catalog_permission_id'];
        $PermissionPerUserSupport->create_date = time();
        $PermissionPerUserSupport->status = $permission['checked'] == 'true' ? 1 : 0;
        $PermissionPerUserSupport->save();
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 