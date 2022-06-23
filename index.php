<?php
/*
 *474Arnette***
 */
session_start();
require 'vendor/autoload.php';
require 'webapp/HarvestAPI.class.php';
require 'webapp/objects/DataPDO.class.php';
require 'webapp/objects/Authorizations.class.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Google\Cloud\Logging\LoggingClient;


# [START gae_slim_front_controller]
$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$logging = new LoggingClient([
    'projectId' => 'parallax-benchmark'
]);

$logger = $logging->psrLogger('app');

$twig = Twig::create('templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', function ($request, $response, $args) {
    $view = Twig::fromRequest($request);

    return $view->render($response, 'welcome.html', []);
})->setName('welcome');

$app->get('/auth/', function ($request, $response, $args) {   
    $view = Twig::fromRequest($request);

    if(isset( $_SESSION['authorization_id']) &&  $_SESSION['authorization_id'] != ''){
        $id = $_SESSION['authorization_id'];
        return $view->render($response, 'authorized.html', [
            'id' => $id
        ]);
    }
    
    //Get the Scope & Code
    if(isset($_GET) && is_array($_GET) && isset($_GET['scope']) && $_GET['scope'] != ''){
        if(isset($_GET['code']) && $_GET['code'] != ''){
            $scope = $_GET['scope'];
            $code = $_GET['code'];

            //Get the access tokens
            $harvest = new HarvestAPI();
            $tokens = $harvest->getAccessTokens($code);

            if(isset($tokens) && is_object($tokens)){
                $hauth = new Authorizations();
                $hauth->addAuthorization($tokens->access_token,$tokens->refresh_token,$tokens->expires_in,$scope,$code);
                $_SESSION['authorization_id'] = $hauth->getID();
            }
        }
    }
    
    

})->setName('welcome');

$app->get('/hello/{name}', function ($request, $response, $args) {
    $view = Twig::fromRequest($request);
    // $harvest = new HarvestAPI();
    // $users =  $harvest->getUsers();
    // var_export($users);

    return $view->render($response, 'profile.html', [
        'name' => $args['name'],
        'dates' => '"01/01/2003", "02/01/2003", "03/01/2003", "04/01/2003", "05/01/2003", "06/01/2003", "07/01/2003", "08/01/2003", "09/01/2003", "10/01/2003","11/01/2003"'
    ]);

})->setName('profile');

$app->get('/data/utilization', function ($request, $response, $args) {

    $data[] = array("name"=>"Tom", "data"=> array());
    $data[] = array("name"=>"John", "data"=> array());
    $data[] = array("name"=>"Ed", "data"=> array());
    $data[] = array("name"=>"Laura", "data"=> array());
    $data[] = array("name"=>"Bridge", "data"=> array());
    $data[] = array("name"=>"Katie", "data"=> array());
    $data[] = array("name"=>"Tree", "data"=> array());

    foreach($data as &$d){
        for($i=1;$i<11;$i++){
            $d['data'][] = array('x' => strval("Week $i"),
                                 'y' => rand(20,90));
        }
    }
    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);

})->setName('utilization');

$app->get('/data/utilbench', function ($request, $response, $args) {

   
    $payload = '[{"name":"Actual","type":"column","data":[70,69,72,73,73,72,71,77,71,69,72]},
                {"name":"Herioc < 65","type":"line","data":[65,65,65,65,65,65,65,65,65,65,65]},
                {"name":"Operational >= 65","type":"line","data":[75,75,75,75,75,75,75,75,75,75,75]},
                {"name":"Strategic >= 85","type":"line","data":[85,85,85,85,85,85,85,85,85,85,85]}]';

    $response->getBody()->write($payload);
    return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);

})->setName('utilbench');

// Run app
$app->run();
# [END gae_slim_front_controller]
