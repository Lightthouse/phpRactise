<?php

namespace NTSchool\Action;

use GuzzleHttp\Psr7\ServerRequest;
use NTSchool\Model\Post;

class PostGetAction
{
    public function __invoke(ServerRequest $request){
        $post = Post::find($request->getAttribute('id'));
        return view('post_get',[
            'post'  => $post
        ]);
    }
}
