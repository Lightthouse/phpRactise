<?php

namespace NTSchool\Action;

use GuzzleHttp\Psr7\ServerRequest;
use NTSchool\Hash\HashInterface;
use NTSchool\Randomizer\RandomizerInterface;
use Illuminate\Validation\ValidationException;
use NTSchool\Model\User;

class SignUpAction
{
    public $hash;
    public $validator;
    public $randomizer;

    public function __construct(HashInterface $hash, $validator,RandomizerInterface $randomizer){
        $this->hash = $hash;
        $this->validator = $validator;
        $this->randomizer = $randomizer;
    }

    public function __invoke(ServerRequest $request){
        $bag = new \Illuminate\Support\MessageBag();
        $data = [];
        if($request->getMethod() == "POST"){
            $data = $request->getParsedBody();
            try{
                $this->validator->validate($data,[
                    'email' =>['required','email','unique:users,email'],
                    'password' =>['required','min:6','confirmed'],
                    'password_confirmation' =>['required','min:6']
                ]);
                $user = new User();
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->password = $this->hash->hash($data['password']) ;
                $user->email = $data['email'];
                $user->created_at = date('Y-m-d H:i:s');
                $user->code = $this->randomizer->generate(10);
                $user->confirmed = 0;
                $user->save();
                header('Location: /');

            }
            catch (ValidationException $exception){
                $bag = $exception->validator->errors();
            }
        }
        return view('sign_up',[
            'errors' => $bag,
            'data' => $data
        ]);
    }
}
