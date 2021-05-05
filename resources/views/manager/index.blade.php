@extends('layouts.app')
@section('content')

<div class="container-fluid container2">


    <div class="container">

        <div class="row">
            <div class="col-3" style="background-color:#005387 ; padding-right: 0px; ">


                <br>
                <div style="padding-right: 15px;">
                    <h1 class="cuerpo2  " style="color:#f6f9fc!important;"><strong> {{ trans('home.lbl_informacion_proceso') }}  </strong> </h1>

                    <div class="progress">
                        <div class="bg-info progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">35%</div>
                    </div>
                </div>

                <br><br>

                <ul class="list-group list-group-flush" aria-current="true">
                    <a href="home" class="list-group-item d-flex justify-content-between align-items-center  ">{{ trans('home.lbl_informacion_general') }}
                        <i class="fa fa-check-circle-o completado" aria-hidden="true"></i>
                    </a>
                    <a href="ifoadd" class="list-group-item d-flex justify-content-between align-items-center  active ">{{ trans('home.lbl_informacion_adicional') }}
                        <i class="fa fa-check-circle-o completado " aria-hidden="true"></i>
                    </a>
                    <a href="#" class="list-group-item d-flex justify-content-between align-items-center active">{{ trans('home.lbl_calidad_economica') }}
                        <i class="fa fa-check-circle-o completado" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="list-group-item d-flex justify-content-between align-items-center active">{{ trans('home.lbl_accionistas_socios') }}
                        <i class="fa fa-exclamation-triangle pendiente" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="list-group-item d-flex justify-content-between align-items-center active">{{ trans('home.lbl_junta_directiva') }}
                        <i class="fa fa-exclamation-triangle pendiente " aria-hidden="true"></i>
                    </a>
                    <a href="#" class="list-group-item d-flex justify-content-between align-items-center active">{{ trans('home.lbl_representant_legal') }}
                        <i class="fa fa-exclamation-triangle pendiente" aria-hidden="true"></i> </a>
                        <a href="load" class="list-group-item d-flex justify-content-between align-items-center active">{{ trans('home.lbl_load') }}
                            <i class="fa fa-exclamation-triangle pendiente" aria-hidden="true"></i> </a>
                </ul>


            </div>
            <div class="col-9">
                <br>
                <h2 class="cuerpo3 "><strong>{{ trans('home.lbl_informacion_general') }} </strong></h2>
                <hr>
                <br>
                <form class="row  cuerpo2">
                    <div class="container">

                        <div class="row">

                            <div class="col-sm">
                                <label for="exampleFormControlInput1" class="form-label">{{ trans('home.lbl_pais')}} </label>
                                <input type="text" class="form-control" value="COLOMBIA" readonly>
                            </div>

                            <div class="col-sm">
                                <label for="exampleFormControlInput1" class="form-label">{{ trans('home.lbl_fecha')}} </label>
                                <input type="date" class="form-control">
                            </div>

                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_tipo_identificacion')}}                                </label>

                                <select class=" form-control form-select form-select-sm">
                                    <option value="1">NIT</option>
                                    <option value="2">CC - Cedula de Ciudadania</option>
                                    <option value="3">CE - Cedula de Extrangeria</option>
                                </select>
                            </div>
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_numero_documento')}}
                                </label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_tipo_persona')}}
                                </label>

                                <select class=" form-control form-select form-select-sm">
                                   
                                    <option value="2">Naturarl</option>
                                    <option value="3">Juridica</option>
                                </select>
                            </div>

                        </div>

                        <br>
                        <div class="row">
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_nombre')}} </label>

                                </label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_apellido')}}

                                </label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_persona_publica')}}  </label>

                                <select class=" form-control form-select form-select-sm">
                                        <option value="2">NO</option>
                                        <option value="3">SI</option>
                                </select>
                            </div>

                        </div>
                        
                        <br>

                        <div class="row">
                            <div class="col-sm">
                                <label class="form-label">  {{ trans('home.lbl_razon_social')}}  </label>

                                </label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-sm">
                                <label class="form-label">{{ trans('home.lbl_registro_mercantil')}}</label>
                                <input type="text" class="form-control">
                            </div>
                           

                        </div>





                    </div>



                    <div class="col-12">
                        <br>
                        <button type="submit" class="btn btn-primary">{{ trans('home.lbl_siguiente') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>




</div>












@endsection
