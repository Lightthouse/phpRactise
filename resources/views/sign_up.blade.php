@extends('layout')

@section('content')


    <form enctype="application/x-www-form-urlencoded" method="post">
    <div class="form-group">
        <label for="firts_name">First Name:</label>
        <input name="first_name" type="text" class="form-control" id="fn" value="@isset($data['first_name']){{$data['first_name']}}@endisset">
    </div>
    <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input name="last_name" type="text" class="form-control" id="ln" value="@isset($data['last_name']){{$data['last_name']}}@endisset">
    </div>
        @if($errors->has('email'))
            <ul>
                @foreach($errors->get('email') as $mess)
                    <li>{{$mess}}</li>
                @endforeach
            </ul>
        @endif

        <div class="form-group">
        <label for="email">Email address:</label>
        <input name="email" type="text" class="form-control" id="email" value="@isset($data['email']){{$data['email']}}@endisset">
    </div>
        @if($errors->has('password'))
            <ul>
                @foreach($errors->get('password') as $mess)
                    <li>{{$mess}}</li>
                @endforeach
            </ul>
        @endif
    <div class="form-group">
        <label for="pwd">Password:</label>
        <input name="password" type="password" class="form-control" id="pwd" value="">
    </div>
        @if($errors->has('password_confirmation'))
            <ul>
                @foreach($errors->get('password_confirmation') as $mess)
                    <li>{{$mess}}</li>
                @endforeach
            </ul>
        @endif

        <div class="form-group">
        <label for="pwd_cnf">Password Confirmation:</label>
        <input name="password_confirmation" type="password" class="form-control" id="pwd_cnf" >
    </div>
    <button type="submit" class="btn btn-primary">Sign Up</button>
</form>
@endsection
