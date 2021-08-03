<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Sica</title>
   
      <!-- Styles -->
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
      <link href="{{ asset('css/layaout.css') }}" rel="stylesheet">
    <!--Incorporacion Alertify-->
    
         <!--Incorporación de selectpicker-->
    <link rel="stylesheet" href="{{ asset('libraries/selectpicker/css/select.min.css') }}">
    <!-- JavaScript -->
    <script src="{{ asset('js/alertify.js')}}"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{asset('js/alertify.core.css')}}"/>
    <!-- Default theme -->
    <link rel="stylesheet" href="{{asset('js/alertify.default.css')}}"/>

    <link href="{{ asset('libraries/FontAwesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!--<link href="{{ asset('libraries/flag-icon-css-master/css/flag-icon.min') }}" rel="stylesheet">-->
   {{--  <link href="{{ asset('libraries/flag-icon-css-master/css/flag-icon.css') }}" rel="stylesheet"> --}}
    
    
    <link href="{{ asset('resources/favicon.ico') }}" rel="shortcut icon" type="image/vnd.microsoft.icon" />
  
   
    <script src="{{ asset('js/jquery-3.3.1.js') }}"></script>
  
    
    <!----summernote-0.8.18-dist -->

 
    <link href="{{ asset('libraries/summernote-0.8.18-dist/summernote.min.css')}}"  rel="stylesheet">
    <script src="{{ asset('libraries/summernote-0.8.18-dist/summernote.min.js')}}" ></script>
    <!-- include summernote-ko-KR -->
    <script src="{{ asset('libraries/summernote-0.8.18-dist/lang/summernote-es-ES.js')}}" ></script>
    

    <!--switchery and notification toastr -->
    <link href="{{ asset('libraries/switchery/switchery.min.css') }}" rel="stylesheet">
    <script src="{{ asset('libraries/switchery/switchery.min.js') }}"></script>
    <link href="{{ asset('libraries/toastr/toastr.min.css') }}" rel="stylesheet">
    <script src="{{ asset('libraries/toastr/toastr.min.js') }}"></script>
    <!-- DataTable and select -->
    <script src="{{ asset('libraries/selectpicker/js/popper.min.js') }}"></script>
    <link href="{{ asset('libraries/MinimalTreeTable/jqueryTreetable.css') }}" rel="stylesheet">
    <script src="{{ asset('libraries/dataTable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('libraries/dataTable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('libraries/select2/select2.min.js') }}"></script>
    <link href="{{ asset('libraries/select2/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('libraries/selectpicker/js/select.min.js') }}"></script>
    <!-- nav -->
    <link href="{{ asset('libraries/sidebar/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libraries/sidebar/style3.css') }}" rel="stylesheet">
    <script src="{{ asset('libraries/sidebar/bootstrap.min.js') }}"></script>
    <script src="{{ asset('libraries/sidebar/popper.min.js') }}"></script>
    <script src="{{ asset('libraries/sidebar/CustomScrollbar.min.js') }}"></script>
    <link href="{{ asset('libraries/sidebar/CustomScrollbar.min.css') }}" rel="stylesheet">
    <script src="{{ asset('libraries/sidebar/CustomScrollbar.min.js') }}"></script>
   
    <script src="https://unpkg.com/bootstrap-show-password@1.2.1/dist/bootstrap-show-password.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" integrity="sha512-HWlJyU4ut5HkEj0QsK/IxBCY55n5ZpskyjVlAoV9Z7XQwwkqXoYdCIC93/htL3Gu5H3R4an/S0h2NXfbZk3g7w==" crossorigin="anonymous"></script>

    
</head>

<body class="cts-body">

    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar" class="d-block d-sm-block d-md-none d-lg-none d-xl-none ">

            <div id="dismiss">
                <i class="fa fa-arrow-left"></i>
            </div>

            <div class="sidebar-header">
                <a class="navbar-brand" title="Carvajal Tecnolog&iacute;a y servicios">
                    <img src="{{ asset('/resources/carvajal_TYS.png') }}">
                </a>
            </div>

            @guest
                <ul class="list-unstyled components">
                    <li class="active">
                        <a href="{{ route('login') }}"><i class="fa fa-user" aria-hidden="true"></i> {{ __('Iniciar sesión') }}</a>
                    </li>
                    
                        
                    
                </ul>
            @else
                
                <ul class="list-unstyled components">
                    <li class="active">
            
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            {{ __('Cerrar Sesión') }} | <span class="fa fa-sign-out"></span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        
                    </li>
                </ul>

                <ul class="list-unstyled components">
                    @can('AutorizadoSidebar')
                        @foreach ($menus as $key => $item)
                            @if ($item['parent_id'] != 0)
                            @break
                            @endif
                            
                            @include('partials.menu-nav', ['item' => $item])
                        @endforeach
                    @endcan
                </ul>
            @endguest
        </nav>
        <!--end Sidebar -->
        <!-- nav -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">

            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home')}}" title="Carvajal" width="50%" >
                    <img src="{{ asset('/resources/carvajal_TYS_1.png') }}" />
                </a>
                
                @guest
               
                @else

                    <button type="button" id="sidebarCollapse" class="btn btn-info menuMobile  d-block d-sm-block d-md-none d-lg-none d-xl-none">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="d-none d-sm-none d-md-block d-lg-block d-xl-block menuWeb collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            @can('Autorizado')
                                @foreach ($menus as $key => $item)
                                    @if ($item['parent_id'] != 0)
                                    @break
                                    @endif
                                    @include('partials.menu-item', ['item' => $item])
                                @endforeach
                            @endcan
                        </ul>

                        <ul class="navbar-nav ml-auto">
                            @php
                            //Validar si el usuario es aprobador de algun flujo, para saber si mostrar las notificaciones
                            $aprobador = DB::table('ohxqc_config_solicitud_empresas')
                            ->where('usuario_aprobador_id', auth()->user()->id)->get();
                            if(count($aprobador) > 0){
                                $notificaciones = DB::table('ohxqc_notificacion_solicitud')
                                ->where('id_aprobador', auth()->user()->id)
                                ->orderBy('id_solicitante', 'DESC')
                                ->get();
                                
                                $arrayInfoNoti = array();
                                if(count($notificaciones) > 0){
                                foreach($notificaciones as $noti){
                                        $infoSolicitud = DB::table('ohxqc_solicitud_por_aprobar')
                                        ->select('id_solicitud', 'fecha_registro', 'estado as estGeneral', 'niveles', 'nivel_actual')
                                        ->where('tipo_visitante', $noti->id_tipov)
                                        ->where('sede_id', $noti->id_sede)
                                        ->where('id_solicitud', $noti->id_solicitante)
                                        ->get();
                                        foreach($infoSolicitud as $info){

                                            $arrayInfoNoti[] = array('id_solicitud'=>$info->id_solicitud, 'fecha_registro'=>$info->fecha_registro, 'estGeneral'=>$info->estGeneral,'niveles'=>$info->niveles,'nivel_actual'=>$info->nivel_actual,'nivel_aprobador'=>$noti->nivel_aprobador, 'url' => $noti->url, 'visto'=>$noti->visto);
                                        }
                                    }
                                // echo "<pre>";
                                    //print_r($arrayInfoNoti);
                                    $cantNotificaciones = 0;
                                    $cantTotalSoli = 0;
                                    foreach($arrayInfoNoti as $inf){
                                        if ($inf['nivel_actual'] == $inf['nivel_aprobador'] && $inf['estGeneral'] == 'Pendiente'){
                                            $cantNotificaciones++;
                                        }
                                
                                        $cantTotalSoli++;
                                    }

                                }else{
                                    $cantTotalSoli = 0;
                                    $cantNotificaciones = 0;
                                }
                            
                            }else{
                                $aprobador = false;
                                $cantTotalSoli = 0;
                                $cantNotificaciones = 0;
                                $arrayInfoNoti = array();
                            }
                            @endphp 
                                @if ($aprobador)
                                    <li class="nav-item">
                                        <a data-toggle="modal" data-target="#modalNotificacion2" href="#"><i class="fa fa-bell" aria-hidden="true"></i><span> ({{$cantNotificaciones}}) </span> </a>
                                    </li>
                                @endif
                            <li class="nav-item">
                                <a>  {{ ' '. Auth::user()->name }}   &nbsp;</a>
                            </li>
                            <li>
                                <a  href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Salir">
                                        <span class="fa fa-sign-out"></span>   |
                                </a>
        
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                            </li>
                        </ul> 
                    </div>
                    
                @endguest
                <ul class="navbar-nav ml-auto">
                    <!-- <li class="nav-item">
                         <a class="nav-link" href="{{ route('login') }}"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;{{ __(' Iniciar sesión') }}</a>
                     </li>-->
                   
                     <li class="nav-item dropdown">
                         <a class="nav-link  " href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="false">
                            &nbsp; <i class="fa fa-language" aria-hidden="true"></i> @php
                                  
                                  if ( session('lang')  == 'es'){
 
                                     echo  '&nbsp;&nbsp;<span class="flag-icon flag-icon-co flag-icon-squared"></span>'  ;
                                  }
                                  if ( session('lang')  == 'en'){
 
                                   echo  '&nbsp;&nbsp;<span class="flag-icon flag-icon-us flag-icon-squared"></span>' ;
                                 }
                                 if ( session('lang')  == 'pr'){
 
                                 echo  '&nbsp;&nbsp;<span class="flag-icon flag-icon-br flag-icon-squared"></span>' ;
                                 }
                                  
                              @endphp
                         </a>
                         <ul class="dropdown-menu pull-right" style="right: 0; left: auto;"  aria-labelledby="navbarDropdownMenuLink">
                           
                           <li><a class="dropdown-item" href="{{ url('lang', ['es']) }}"><span class="flag-icon flag-icon-co flag-icon-squared"></span> Español</a></li>
                           <li><a class="dropdown-item" href="{{ url('lang', ['en']) }}"> <span class="flag-icon flag-icon-us flag-icon-squared"></span> Ingles</a></li>
                         
                           
                           
                         </ul> 
                 </ul>
            </div>
            
        </nav>
        <div  class = 't'>
          <div class = 'menu-cts'></div> 
         
        </div>
        <!-- End nav -->
   
        <main class="py-4 principal-container">
            @yield('content')
        </main>
        @if (isset($aprobador) && $aprobador)
           <!--Modal notificacion-->
           <div class="modal fade " id="modalNotificacion2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Solicitudes Por Validar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <div class="row">
                            <div class="col-xs-12 col-md-8 col-lg-8"></div>
                            <div class="col-xs-12 col-md-4 col-lg-4">
                                <h6>Total Solicitudes En Mi Flujo: <b>{{$cantTotalSoli}}</b></h6>
                                
                            </div>
                            
                        </div>
                        <table  id="tblistado" class="table table-light">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#Solicitud</th>
                                    <th>Fecha Registro</th>
                                    <th>Estado General</th>
                                    <th>Flujo Actual</th>
                                    <th>¿Validada en mi nivel?</th>
                                    <th>Visualizar</th>
                                </tr>
                            </thead>
                            <tbody>
                              
                                @foreach ($arrayInfoNoti as $noti)
                                @php
                                    if ($noti['nivel_actual'] == $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Pendiente'){
                                        $respuesta = "NO";
                                    }elseif ($noti['nivel_actual'] == $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Aprobado'){
                                        $respuesta = "SI";
                                    } elseif ($noti['nivel_actual'] != $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Aprobado' || $noti['nivel_actual'] != $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Pendiente'){
                                        $respuesta = "SI";
                                    }elseif ($noti['nivel_actual'] == $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Rechazado'){
                                        $respuesta = "SI";
                                    }elseif($noti['nivel_actual'] != $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Rechazado' && $noti['nivel_actual'] < $noti['nivel_aprobador']){
                                        $respuesta = "NO";
                                    }elseif($noti['nivel_actual'] != $noti['nivel_aprobador'] && $noti['estGeneral'] == 'Rechazado' && $noti['nivel_actual'] > $noti['nivel_aprobador']){
                                        $respuesta = "SI";
                                    }
                                    if($respuesta == "NO" && $noti['visto'] == "N"){
                                        $color = "rgba(0,0,0,.075)";
                                    }else if($respuesta == "NO" && $noti['visto'] == "S" && $noti['estGeneral'] == "Pendiente"){
                                        $color = "rgb(255 247 2 / 28%)";
                                    }else{
                                        $color = "";
                                    }
                                @endphp
                                    <tr style="background: {{$color}}">
                                        <td>{{$noti['id_solicitud']}}</td>
                                        <td>{{$noti['fecha_registro']}}</td>
                                        @switch($noti['estGeneral'])
                                            @case('Aprobado')
                                                <td><span class="badge badge-success">{{$noti['estGeneral']}}</span></td>
                                                @break
                                            @case('Pendiente')
                                                <td><span class="badge badge-warning">{{$noti['estGeneral']}}</span></td>
                                                @break
                                            @case('Rechazado')
                                                <td><span class="badge badge-danger">{{$noti['estGeneral']}}</span></td>
                                                @break
                                                
                                        @endswitch
                                            <td>{{$noti['nivel_actual'].'/'.$noti['niveles']}}</td>
                                            <td>{{$respuesta}}</td>
                                     
                                        <td><a onclick="visto({{$noti['id_solicitud']}})" href="{{$noti['url']}}" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if ($cantTotalSoli == 0)
                        <p>Actualmente no hay solicitudes por validar en el flujo al que perteneces</p>
                         @endif
                       
                       
                      </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            </div>
             </div>
        @endif

    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#sidebar").mCustomScrollbar({
                theme: "minimal"
            });

            $('#dismiss, .overlay').on('click', function () {
                $('#sidebar').removeClass('active');
                $('.overlay').removeClass('active');
            });

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $(this).toggleClass('active');
            });

            $('.linkTitle').on('click',function(){
                var linkTitle = $(this).data('href');
                window.location.href = linkTitle;
            });
        });

        function visto(idsolicitud)
         {
            var token = '{{csrf_token()}}';
            $.ajax({
                    type:  'POST',
                    async: true,
                    url: "{{route('asignar.visto')}}", 
                    data: {'id':idsolicitud,_token:token},
                    cache: false,
                    success: function(response){
                        //toastr.success('Visto');
                        },
                    error:function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError);
                        }
                    });
         }
    </script>

    <script src="{{ asset('js/layaout.js') }}"></script>
    <script>
            const $dropdown = $(".dropdown");
            const $dropdownToggle = $(".dropdown-toggle");
            const $dropdownMenu = $(".dropdown-menu");
            const showClass = "show";
            console.log("HSCT");
            $(window).on("load resize", function() {
            if (this.matchMedia("(min-width: 100%)").matches) {
                $dropdown.hover(
                function() {
                    const $this = $(this);
                   // $this.addClass(showClass);
                    $this.find($dropdownToggle).attr("aria-expanded", "true");
                   // $this.find($dropdownMenu).addClass(showClass);
                },
                function() {
                    const $this = $(this);
                    //$this.removeClass(showClass);
                    $this.find($dropdownToggle).attr("aria-expanded", "false");
                    //$this.find($dropdownMenu).removeClass(showClass);
                }
                );
            } else {
                $dropdown.off("mouseenter mouseleave");
            }
            });
          

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})






    </script>





</body>


</html>
