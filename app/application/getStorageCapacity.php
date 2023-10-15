<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	$StoragePerUser = new DummieTrading\StoragePerUser;

	if($data['path'] = $StoragePerUser->getStoragePerUserId($UserLogin->company_id))
	{
		if(!$data['full_path'])
		{
			$data['full_path'] = TO_ROOT.$StoragePerUser->getStoragePath($data['path']);
		}


		$data['max_size'] = HCStudio\Util::formatSizeUnits(DummieTrading\StoragePerUser::DEFAULT_DISK_SIZE);
		$data['size'] = HCStudio\Util::formatSizeUnits($StoragePerUser->getDiskUsed($data['full_path']));
        $data['percentaje'] = $StoragePerUser->getPercentajeDiskUsed($data['full_path']);
		$data['r'] = 'DATA_OK';
		$data['s'] = 1;
	} else {
		$data['r'] = 'NOT_PATH';
		$data['s'] = 0;
	}
	
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 