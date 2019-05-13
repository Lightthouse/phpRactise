<?php

namespace NTSchool\Hash;

class Bcrypt implements HashInterface
{
    const OPTION_COST = 'cost';
    const OPTION_SALT = 'salt';
    public function verify(string $password, string $hash) : bool{
       return  password_verify($password,$hash);
    }

    /**
     * @param string $password
     * @param array $options
     * @return bool|string
     * @throws \Exception
     */
    public function hash(string $password, array $options = []){
        if(!empty($options)){
            foreach ($options as $option) {
                if (!in_array($option, [self::OPTION_COST, self::OPTION_SALT])){
               throw new \Exception('wrong parameters!');
                }
            }
        }

        return password_hash($password,PASSWORD_BCRYPT,$options);
    }
}
