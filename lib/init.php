<?php
if(session_id() == "")
{
    session_start();
}

require_once __DIR__ . '/Vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$array_folders = array("", "Orange", "FormGenerator", "Configs", "");
define("OFG_CONFIG_DIR", __DIR__ . implode(DIRECTORY_SEPARATOR, $array_folders));
define("OFG_CONFIG_FILE", "defaultform.yml");
define("OFG_VALIDATION_CONFIG_FILE", "validators.yml");

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'FormGenerator' => __DIR__ . '/Orange',
    'Symfony'  => __DIR__ . '/Vendors'
));
$loader->register();
