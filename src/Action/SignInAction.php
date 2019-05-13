<?php

namespace NTSchool\Action;

use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Validation\ValidationException;
use NTSchool\Model\User;
use NTSchool\Hash\HashInterface;

class SignInAction
{
    protected $hash;
    protected $validator;

    public function __construct(HashInterface $hash, $validator){
        $this->hash = $hash;
        $this->validator = $validator;
    }

    public function __invoke(ServerRequest $request){
        $data = [];
        $bag = new \Illuminate\Support\MessageBag;

        if($request->getMethod() == 'POST'){
            $data = $request->getParsedBody();
            try{

                $this->validator->validate(
                    $data,
                    [
                        'email' => ['required','email'],
                        'password' => ['required','min:6']
                    ]);
                $user = User::where('email', '=',$data['email'] )->get()->first();

                if($this->hash->verify($data['password'],$user->password)){
                    header('Location: /');
                }
                else{
                    $data['wrong_data'] = 'неверные данные';
                }


            }
            catch(ValidationException $exception){
                $bag = $exception->validator->errors();
            }
        }

        return view('sign_in',[
            'errors' => $bag,
            'data' => $data
        ]);
    }
}
