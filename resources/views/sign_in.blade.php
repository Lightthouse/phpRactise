@extends('layout')

@section('content')
    <h2>
        @isset($data['wrong_data']){{$data['wrong_data']}}@endisset
    </h2>


    @if($errors->has('email'))
        <ul>
            @foreach($errors->get('email') as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    @endif
<form method="post"  >
    <div class="form-group">
        <label for="email">Email address:</label>
        <input name="email" type="email" class="form-control" id="email" value="@isset($data['email']){{$data['email']}}@endisset">
    </div>
    @if($errors->has('password'))
        <ul>
            @foreach($errors->get('password') as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    @endif
    <div class="form-group">
        <label for="pwd">Password:</label>
        <input name="password" type="password" class="form-control" id="pwd">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection
