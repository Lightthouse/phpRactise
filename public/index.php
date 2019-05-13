<?php
    require_once "../vendor/autoload.php";
    require_once "../config/dotenv.php";
    require_once "../config/database.php";
    require_once "../config/view.php";
    require_once "../config/router.php";
    require_once "../config/container.php";

$serverRequest = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();

$matcher = $routerContainer->getmatcher();

if($action = $matcher->match($serverRequest)){
    foreach($action->attributes as $name => $attribute){
        $serverRequest = $serverRequest->withAttribute($name,$attribute);
    }
    if($container->has($action->handler)){
        $action = $container->get($action->handler);
    }
    else{
        $action = new $action->handler;
    }

    $response = new \GuzzleHttp\Psr7\Response();
    $response->getBody()->write(call_user_func($action,$serverRequest));

    echo $response->getBody();
}
