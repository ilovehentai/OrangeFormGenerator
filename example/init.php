<?php
if(session_id() == "")
{
    session_start();
}

require_once __DIR__ . '/../lib/Vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'FormGenerator' => __DIR__ . '/../lib/Orange',
    'Symfony'  => __DIR__ . '/../lib/Vendors'
));
$loader->register();
