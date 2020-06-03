@if (Auth::user()->is_favorite($micropost->id))
    {{-- お気に入り削除のボタン --}}
    {!! Form::open(['route' => ['favorites.unfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('Unfavorite', ['class' => "btn btn-success btn-sm mr-2"]) !!}
    {!! Form::close() !!}
@else
    {{-- お気に入り追加のボタン --}}
    {!! Form::open(['route' => ['favorites.favorite', $micropost->id]]) !!}
        {!! Form::submit('Favorite', ['class' => "btn btn-light btn-sm mr-2"]) !!}
    {!! Form::close() !!}
@endif
            