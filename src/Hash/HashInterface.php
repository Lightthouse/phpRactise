<?php

namespace NTSchool\Hash;

interface HashInterface{
    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify(string $password, string $hash) : bool;

    public function hash(string $password,array $options = []) ;
}
