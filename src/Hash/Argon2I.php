<?php

namespace NTSchool\Hash;

class Argon2I implements HashInterface
{
    const OPTION_MEMORY_COST = 'memory_cost';
    const OPTION_TIME_SALT = 'time_cost';
    const THREADS = 'threads';
    public function verify(string $password, string $hash) : bool{
        return password_verify($password,$hash);
    }


    public function hash(string $password, array $options = []){
        if(!empty($options)){
            foreach ($options as $option) {
                if (!in_array($option, [self::OPTION_MEMORY_COST, self::OPTION_TIME_SALT, self::THREADS])){
                    throw new \Exception('wrong parameters!');
                }
            }
        }

        return password_hash($password,PASSWORD_ARGON2I,$options);
    }
}
