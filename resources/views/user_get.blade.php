@extends ('layout')

@section('content')
    @empty($user)
        <p>Пользователя не существует</p>
        @else
        <ul>
            <li>{{$user->first_name}}</li>
            <li>{{$user->last_name}}</li>
            <li>{{$user->email}}</li>
            <li>{{$user->created_at}}</li>
        </ul>

        <p>roles</p>
        <ul>
            @foreach($user->permissions as $permission)
                <li>{{$permission->permissions_name}}</li>
            @endforeach
        </ul>
    @endempty
@endsection
