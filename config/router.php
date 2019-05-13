<?php

$routerContainer = new \Aura\Router\RouterContainer();
$router = $routerContainer->getMap();

$router->get('index','/test',\NTSchool\Action\IndexAction::class);
$router->get('home','/',\NTSchool\Action\HomeAction::class);


$router->get('user_get','/users/{id}',\NTSchool\Action\UserGetAction::class);

$router->get('sign_in','/signIn',\NTSchool\Action\SignInAction::class);
$router->post('sign_in.post','/signIn',\NTSchool\Action\SignInAction::class);

$router->get('sign_up','/signUp',\NTSchool\Action\SignUpAction::class);
$router->post('sign_up.post','/signUp',\NTSchool\Action\SignUpAction::class);

$router->get('post_get','/posts/{id}',\NTSchool\Action\PostGetAction::class);
