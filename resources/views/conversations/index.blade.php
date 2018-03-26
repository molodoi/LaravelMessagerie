@extends('layouts.app')

@section('content')
    <div class="container">
        @include('conversations/users-list', ['users' => $users, 'unread' => $unread])
    </div>
@endsection