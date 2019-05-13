<?php

use Aura\Di\ContainerBuilder;

$builder = new ContainerBuilder();
$container = $builder->newInstance();

$container->set(\NTSchool\Hash\HashInterface::class, function(){
    return new \NTSchool\Hash\Bcrypt();
});

$container->set(\NTSchool\Randomizer\RandomizerInterface::class, function(){
    return new \NTSchool\Randomizer\RandomizerInternet();
});

$container->set('validator', function() use($capsule){
    $filesystem = new \Illuminate\Filesystem\Filesystem();
    $loader = new \Illuminate\Translation\FileLoader($filesystem, dirname(dirname(__FILE__)).'/resources/lang');
    $loader->addNamespace('lang',dirname(dirname(__FILE__)).'/resources/lang');
    $loader->load($lang = 'ru', $group = 'validation', $namespace = 'lang');

    $factory = new \Illuminate\Translation\Translator($loader,'ru');
    $validator = new \Illuminate\Validation\Factory($factory);

    $databasePresenceVerifier = new \Illuminate\Validation\DatabasePresenceVerifier($capsule->getDatabaseManager());
    $validator->setPresenceVerifier($databasePresenceVerifier);

    return $validator;
});

$container->set(\NTSchool\Action\SignInAction::class, function() use($container){
    $hash = $container->get(\NTSchool\Hash\HashInterface::class);
    $validator = $container->get('validator');
    $action = new \NTSchool\Action\SignInAction($hash,$validator);
    return $action;
});

$container->set(\NTSchool\Action\SignUpAction::class, function() use($container){

    return new \NTSchool\Action\SignUpAction(
        $container->get(\NTSchool\Hash\HashInterface::class),
        $container->get('validator'),
        $container->get(\NTSchool\Randomizer\RandomizerInterface::class));
});
