@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <embed  neme = "iframe"  id="ifr" class="iframe-manager wrapper" src="{{$url}}" width="100%" height="900px" scrolling="auto" frameborder="1">
        </div>
    </div>
</div>
@endsection
