@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            @include('conversations.users-list', ['users' => $users, 'unread' => $unread])
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ $user->name }}</div>
                        <div class="panel-body conversations">


                            @foreach($messages as $message)
                                <div class="row">
                                    <div class="col-md-12 {{ $message->from->id == $user->id ? 'offset-md-2 text-right' : '' }}">
                                        <p>
                                            <strong>{{ $message->from->id == $user->id ? 'Moi' : $message->from->name }} {{ \Carbon\Carbon::parse($message->created_at)->format('à H:m - d/m/Y') }}</strong><br>
                                            {!! nl2br(e($message->content)) !!}
                                        </p>
                                    </div>
                                </div>
                                <hr>
                            @endforeach


                            <!-- $errors comes from ShareErrorsOnSession -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="" method="post">
                                {{ csrf_field() }}
                                <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}" >
                                    <textarea name="content" placeholder="Votre message.." class="form-control"></textarea>
                                    @if($errors->has('content'))
                                        <span class="help-block">{{ implode(',', $errors->get('content')) }}</span>
                                    @endif
                                    <br />
                                    <button type="submit" class="btn btn-primary">Envoyer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection