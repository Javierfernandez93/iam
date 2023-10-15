<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	if($data['vcard_per_user_id'])
	{
		if($data['template_id'])
		{
			$Template = new DummieTrading\Template;

			if($Template->loadWhere("template_id = ?",$data['template_id']))
			{
				$VCardPerUser = new DummieTrading\VCardPerUser;

				if($VCardPerUser->loadWhere("vcard_per_user_id = ?",$data['vcard_per_user_id']))
				{	
					$VCardPerUser->template_id = $data['template_id'];

					if($VCardPerUser->save())
					{
						$path = TO_ROOT.DummieTrading\VCardPerUser::PROYECTS_URL."/".$VCardPerUser->getId()."/view/";

						if(file_exists($path) == false)
						{
							if(mkdir($path, 0777, true) == false) 
							{
								$data['s'] = 0;
								$data['r'] = "NOT_MAKE_DIR";
								echo json_encode(HCStudio\Util::compressDataForPhone($data)); 
								die;
							}
						}
					
						$File = JFStudio\File::getInstance();

						$File->setFileName(DummieTrading\VCardPerUser::TEMPLATE_NAME."-".$VCardPerUser->getId());
						$File->setPath($path);
						$File->setContent(getHtml($Template->view));

						if($File->makeFile(true))
						{
							$data['vcard_per_user_id'] = $VCardPerUser->getId();
							$data['s'] = 1;
							$data['r'] = "ASSIGNED_OK";
						} else {
							$data['s'] = 1;
							$data['r'] = "NOT_MAKE_FILE";
						}
					} else {
						$data['s'] = 0;
						$data['r'] = "NOT_SAVE";
					}
				} else {
					$data['r'] = "NOT_PROYECT";
					$data['s'] = 0;		
				}
			} else {
				$data['r'] = "NOT_TEMPLATE";
				$data['s'] = 0;		
			}
		} else {
			$data['r'] = "NOT_TEMPLATE_ID";
			$data['s'] = 0;	
		}
	} else {
		$data['r'] = "NOT_vcard_per_user_id";
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function getHtml($view = null)
{	
	$Layout = JFStudio\Layout::getInstance();

	$Layout->init("",$view,"clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");

	$Layout->setVar([
		
	]);
			
	return $Layout->getHtml();
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 