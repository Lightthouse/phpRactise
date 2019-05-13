@extends('layout')

@section('content')
    @empty($post)
        <p>Статьи не существует</p>
    @else
       <h2>category :{{$post->category->category_name}}</h2>

        <article>{{$post->text}}</article>
        <h2>tags :</h2>
            @foreach($post->tags as $tag)
                <ul>
                    <li>{{$tag->tag_name}}</li>
                </ul>
            @endforeach
    @endempty
@endsection
