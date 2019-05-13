<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' =>'mysql',
    'host' => env('DATABASE_HOST'),
    'database' => env('DATABASE_DBNAME'),
    'username' => env('DATABASE_USERNAME'),
    'password' => env('DATABASE_PASSWORD'),
    'charset' => 'utf8',
    //'collation' => 'utf8_unicode_di',
    'prefix' => ''
]);

$capsule->setEventDispatcher(new Dispatcher((new Container())));
$capsule->setAsGlobal();
$capsule->bootEloquent();
