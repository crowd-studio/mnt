<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SuperController
{
    private $app;
    protected $mustache;

    function __construct($app)
    {
        //Define variables
        $this->app = $app;

        //Load Mustache
        /*Mustache_Autoloader::register();
        $templates = __DIR__.'/../templates/';
        $partials = __DIR__.'/../templates/partials/';
        $this->mustache = new Mustache_Engine(array('template_class_prefix' => '__crumbit__',
            'cache' => __DIR__.'/../tmp/cache/mustache',
            'loader' => new Mustache_Loader_FilesystemLoader($templates),
            'partials_loader' => new Mustache_Loader_FilesystemLoader($partials)
        ));*/
    }

    public function render($res){
        return json_encode($res);
    }

    public function printErrorPage($lang = 'ca', $code = '', $message = ''){
        if($code != '403'){
            $html = $this->mustache->loadTemplate('defaults/layout')->render([
                'title' =>  'ERROR: ' . $message,
                'route' => 'error',
                'content' => $this->mustache->loadTemplate('error/error')->render([
                    'message' => $message,
                    'code' => $code,
                    'trans' => $this->app['trans'][$lang]]),
                'trans' => $this->app['trans'][$lang],
                'DOM' => [
                    'head' => $this->getHeadPage('Error', $message, '', $this->app['request_stack']->getCurrentRequest()->getUri()),
                    'scripts' => $this->getScriptsPage($this->app, $lang)
                ]
            ]);

            return new Response($html, 200);
        } else {
            return $this->app->redirect('/login');
        }
    }

    public function printPage($lang){

/*$app['trans'] = [
    "cat" => json_decode(file_get_contents(__DIR__ . '/../translations/cat.json'), true),
    "es" => json_decode(file_get_contents(__DIR__ . '/../translations/es.json'), true)
];*/


        /*$loader = $this->mustache->loadTemplate('regions/loader')->render([
            'trans' => $this->app['trans'][$lang]['trans']
        ]);
        $html = $this->mustache->loadTemplate('defaults/layout')->render([
                'content' => '',
                'trans' => $this->app['trans'][$lang],
                'loader' => $loader,
                'DOM' => [
                    'head' => $this->getHeadPage($this->app['trans'][$lang]),
                    'scripts' => $this->getScriptsPage($this->app, $lang),
                ]
            ]);*/
        $html = 'OK';
        return new Response($html, 200);
    }

    protected function getHeadPage($trans){
        return $this->mustache->loadTemplate('defaults/head')->render(['trans' => $trans["trans"]]);
    }

    protected function getScriptsPage($object, $lang){
        $route = 'false';
        $session = 'false';
        if(isset($object['session']) && $object['session']->get('routetoken')){
            $route = $this->getRoute($object['session']->get('routetoken'));
            $session = 'true';
        }

        $session = [
            ['key' => 'session', 'value' => $route],
            ['key' => 'route', 'value' => $session],
            ['key' => 'bootstrapped', 'value' => json_encode($object['trans'][$lang])],
            ['key' => 'languages', 'value' => $this->getAllLanguages($lang)],
            ['key' => 'language', 'value' => '"' . $lang . '"']
        ];

        return $this->mustache->loadTemplate('defaults/scripts')->render(['session' => $session]);

    }

    protected function getAllLanguages($lang){
        return json_encode([
            [
                'key' => 'cat',
                'value' => 'cat' == $lang
            ],
            [
                'key' => 'es',
                'value' => 'es' == $lang
            ]
        ]);
    }
}