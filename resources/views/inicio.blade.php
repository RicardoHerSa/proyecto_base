
@extends('layouts.app')
@section('content')
<div class="container overflow-hidden">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <br>
         
            
            <div class="card text-center">
                <h5 class="card-header">Bienvenido</h5>
                <div class="card-body">
                    <div class="container">
                        <div class="row justify-content-md-center">
                         
                          <div class="col col-lg-3">
                            <img class="img-fluid" width="550px" src="{{ asset('/resources/sica.png') }}">
                          </div>
                          
                          <div class="col col-lg-3">
                           
                            <div class="card" style="width: 30rem;">
                                
                                <div class="card-body">
                                  <p style ="color: #000000;  font-size: 20px;" class="card-title"><i class="fa fa-user-circle" aria-hidden="true"></i> {{ Auth::user()->name }}</p>
                                  <p style ="color: #000000;"  class="card-text"><i class="fa fa-envelope" aria-hidden="true"></i> {{ Auth::user()->email }}</p>
                                  <br>
                                  
                                  <h6 class="card-text"> {{ now() }}</h6>
								  <a  type="button" class="btn btn-primary" target="_blank" href="{{ asset('/resources/manual/Manual Sica v2.0.pdf') }}">
								  
								  <i class="fa fa-download" aria-hidden="true"></i>Manual de Uso</a> 
                                  <br>
                                  
                                
                                </div>
                              </div>


                          </div>
                        </div>
                    </div>
                    
                  
                  
                 
                </div>
              </div>
            
        </div>
    </div>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
@include('layouts.footer', ['modulo' => 'unitario'])
@endsection


