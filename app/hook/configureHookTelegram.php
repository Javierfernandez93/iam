<?php define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

d(JFStudio\ApiTelegram::configureHook());