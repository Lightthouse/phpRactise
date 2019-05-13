<?php


namespace NTSchool\Randomizer;


interface RandomizerInterface
{
    public function generate(int $length):string;

}
