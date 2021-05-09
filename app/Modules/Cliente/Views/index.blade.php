@extends('layouts.app')
@section('content')
<div class="container">
@include('layouts.message')
<br>         
<div class="card">
    <div class="card-header">
      Quote
    </div>
    <div class="card-body">
      <blockquote class="blockquote mb-0">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
        <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source Title</cite></footer>
      </blockquote>
    </div>
  </div>
<p>Español: {{trans('langCliente::es.modulo')}}</p><br>
<p>Ingles: {{trans('langCliente::en.modulo')}}</p>
  
</div>

@endsection