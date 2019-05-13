<?php

namespace NTSchool\Action;

use GuzzleHttp\Psr7\ServerRequest;
use NTSchool\Model\User;

class UserGetAction
{
    public function __invoke(ServerRequest $request){
       $user = User::find($request->getAttribute('id'));
        return view('user_get',[
            'user' => $user
        ]);
    }
}
