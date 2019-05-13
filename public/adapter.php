<?php

final class RandomMan{
    private $user;
    private $attr;
    private $generator;

    public function __construct(RandaomUserInterface $generator){
        $this->generator = $generator;
    }
    public function createName(){
        $this->user = $this->generator;
        $text = $this->user->generateUser();
        return $text;
    }
    public function createPhone(){
        $this->attr = $this->generator;
        $text = $this->attr->generateParams();
        return $text;
    }
}

interface RandaomUserInterface{
    public function generateUser();
    public  function generateParams();
}

final class RandomPavliha implements RandaomUserInterface{
    public $gen;
    public $name;
    public $email;

    public function __construct(){
        $this->gen = new \RandomUser\Generator();
    }
    public function generateUser()
    {
        $this->name =  [
            'firstName' => $this->gen->getUser()->getFirstName(),
            'lastName' => $this->gen->getUser()->getLastName()
        ];

        return $this->name;
    }
    public function generateParams(){
        $this->email = [
            'email' => $this->gen->getUser()->getEmail(),
            'phone' => $this->gen->getUser()->getPhone()
        ];
        return $this->email;
    }

}

final class RandomStepan implements RandaomUserInterface{
    public $gen;
    public $name;
    public $email;

    public function __construct(){
        $this->gen = new StepanRandom();
    }
    public function generateUser()
    {
        $this->name =  [
            'firstName' => $this->gen->myFirstName,
            'lastName' => $this->gen->myLastName
        ];

        return $this->name;
    }
    public function generateParams(){
        $this->email = [
            'email' => $this->gen->myPhone,
            'phone' => $this->gen->myEmail
        ];
        return $this->email;
    }

}

class StepanRandom {
    public $myFirstName = 'Stepan';
    public $myLastName = 'Kazantcev';
    public $myPhone = '555-4321';
    public $myEmail = 'Steanda96@yandex.ru';
}

$gen = new RandomMan(new RandomPavliha);
$man = $gen->createPhone();


var_dump($man);
