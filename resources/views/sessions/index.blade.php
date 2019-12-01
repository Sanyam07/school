@extends('layouts.app')

@section('content')

<div class="container">
    
    <div class="row justify-content-center">
        @include('include.left-menu')
        <div class="col-md-8">
            <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <ul class="nav flex-column">
                    @foreach ($schoolSessions as $schoolSession)

                    <li class="nav-item">
                    <a class="nav-link btn btn-block btn-primary" href="{{ url('settings/showsession/' .$schoolSession->id )}}"><span
                        class="nav-link-text">{{ $schoolSession->name }}</span></a>
                    </li>

                    @endforeach
                </ul>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection