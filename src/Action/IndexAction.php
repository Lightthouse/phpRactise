<?php


namespace NTSchool\Action;


use GuzzleHttp\Psr7\ServerRequest;

class indexAction
{
    public function __invoke(ServerRequest $request){
        return view('index');
    }
}
