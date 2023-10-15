<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	if(!empty($_FILES))
	{
		if(is_uploaded_file($_FILES['file']['tmp_name']))
		{
			$StoragePerUser = new DummieTrading\StoragePerUser; 

			if($data['path'] = $StoragePerUser->getStoragePerUserId($UserLogin->company_id))
			{
				if(!$data['full_path'])
				{
					$data['full_path'] = TO_ROOT.$StoragePerUser->getStoragePath($data['path']);
				}
				
				$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

				$source_path = $_FILES['file']['tmp_name'];			
				$target_path = "{$data['full_path']}/{$_FILES['file']['name']}";

				if(move_uploaded_file($source_path, $target_path))
				{
					$data['target_path'] = $target_path;
					$data['r'] = 'UPLOAD_OK';
					$data['s'] = 1;
				} else {
					$data['r'] = 'NOT_UPLOADED';
					$data['s'] = 0;
				}
			} else {
				$data['r'] = 'NOT_PATH';
				$data['s'] = 0;
			}
		} else {
			$data['r'] = 'NOT_FILES_UPLOADED';
			$data['s'] = 0;
		}
	} else {
		$data['r'] = 'NOT_FILES';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 