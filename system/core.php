<?php

function autoloadCore($className) {
	$className = ltrim($className, '\\');
	$fileName  = __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;

	if ($lastNsPos = strrpos($className, '\\'))
	{
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if(stream_resolve_include_path($fileName)) require_once($fileName);
}


# Registramos autoload
spl_autoload_register('autoloadCore');

# Registramos timezone
// if (!date_default_timezone_get('date.timezone')) {
date_default_timezone_set('America/Mexico_City');
// setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');
// }

# Aplicacion en producccion?
define('__DEBUG__', false);
define('LANGUAGE', 'spanish');

#
if(!__DEBUG__) {
	ini_set('error_reporting', E_ALL ^ E_NOTICE);
	ini_set('display_errors', '1');
} else  ini_set('display_errors', '0');


/* main_function */
function debug($var = null,$clean_ob = true) : void
{
	if(isset($var) === true)
	{
		if($clean_ob === true)
		{
			ob_clean();
		}

		echo "<pre>";print_r($var);die;
	}
}

function d($var = null,$clean_ob = null) : void
{
	debug($var,$clean_ob);
}