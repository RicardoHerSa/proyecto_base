@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        
        <div class="col-md-12">
            @include('layouts.message')
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            
            <!-- <iframe src="{{--URL::to('/')--}}/your file path here" width="100%" height="600"></iframe> -->
            <!-- <iframe src="https://es.wikipedia.org" width="100%" height="900px"></iframe>  -->
            <!-- <iframe src="yii/index.php?option=com_categories&extension=com_contact" width="100%" height="900px"></iframe>-->
            
          
            <iframe  src="{{$url}}" width="100%" height="900px" style="border:0px solid lightgrey;"></iframe>
			
            <!--<iframe width="100%" height="100%" src="https://www.youtube-nocookie.com/embed/bQkKB8zpawE?start=55" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>-->
        </div>
    </div>
</div>
@endsection
