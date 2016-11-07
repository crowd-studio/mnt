<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../controllers/SuperController.php';

require_once __DIR__.'/../interfaces/Cache.php';
require_once __DIR__.'/../interfaces/Database.php';
// // require_once __DIR__.'/../interfaces/Parser.php';
require_once __DIR__.'/../interfaces/Trans.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

$app = new Silex\Application();

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app->debug = true;
$app->defaultLanguage = 'es';
$app->enviroment = 'dev'; // 'prod';


if($app->debug)
{
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}

$app->settings = Yaml::parse(file_get_contents(__DIR__ . '/../app/settings.yml'));


// Database
$conn = false;
if(isset($app->settings['database'][$app->enviroment])){
    $connectionParams = $app->settings['database'][$app->enviroment];
    $config = new \Doctrine\DBAL\Configuration();
    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
}


Cache::Instance(__DIR__ . '/../tmp/cache');
Database::Instance($conn);
// // Parser::Instance();
Trans::Instance($app->settings['languages']);

// SuperController
$superController = new SuperController($app);


//Routing
$app->get('/', function (Request $request) use ($app){
    return $app->redirect('/' . $app->defaultLanguage);
});

$app->get('/{lang}', function ($lang) use ($app, $superController){
    //if(array_key_exists($lang, $app['trans'])){
        return $superController->printPage($lang);
    //}
    return $superController->printErrorPage( $app['defaultLanguage'], 200, $app['trans'][$app['defaultLanguage']]);

});

$app->run();