<?php 

require_once "vendor/autoload.php";

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;

$config = ORMSetup::createAnnotationMetadataConfiguration(
    array(__DIR__."/src"),
    $isDevMode,
    $proxyDir,
    $cache,
    $useSimpleAnnotationReader
);

$database_Url = $_ENV["DATABASE_URL"];

$dir = __DIR__;
$dir = str_replace("\\", "/", $dir);

$replace = "/%kernel.project_dir%/";
$database_Url = preg_replace($replace, $dir, $database_Url);



$database = array(
    "url" => $database_Url
);



$entityManager = EntityManager::create($database, $config);
$em = $entityManager;


