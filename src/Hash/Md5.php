<?php

namespace NTSchool\Hash;

class Md5 implements HashInterface
{
    public function verify(string $password, string $hash) : bool {
        if(md5($password) === $hash){
            return true;
        }
        return false;
    }

    public function hash(string $password,array $options = []){
        return md5($password);
    }
}
