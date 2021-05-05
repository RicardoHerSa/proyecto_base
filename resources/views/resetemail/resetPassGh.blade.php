@extends('layouts.app')

@section('content')
<div class="container">

    <div class="container reset-password">
        <div class="row justify-content-center">

            <div class="col-md-10">
                <div class="card">
                    <div class="card-header"><strong><i class="fa fa-user"></i>&nbsp; {{ __(' Administración de Usuario ') }} </strong></div>
                    <div class="card-body"> </div>

                    @if(count($Access) === 0 )
                    <h5 class="row justify-content-center"> <i class="fa fa-lock" aria-hidden="true">&nbsp;&nbsp; </i> El usuario no tiene acceso a esta opción.</h5>
                    <br>
                    <br>
                    @else

                    <div class="row" style="margin: 0em;">
                        <div class="col-xs-12 col-sm-6 col-md-6">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                                </div>
                                <input type="text" id='user_input' class="form-control" placeholder="INGRESAR USUARIO" aria-describedby="sizing-addon3">
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6">

                            <button id="sub" class="btn btn-primary  btn-sm " onclick="BuscarUsuario()">
                                <i class="fa fa-search"></i> <?php echo 'Buscar Usuario' ?>
                            </button>
                            <br><br>

                            <div id="CAR" hidden="hidden">
                                <img src='/cess/aplicacion/images/Carga1.gif' WIDTH=30 HEIGHT=30 id='image_sending'> </img> Cargando ...
                            </div>
                        </div>

                    </div>

                    <div class="row" style="margin: 0em;">

                        <div class="col-xs-12 col-sm-12 col-md-12 well">

                            <div class="row info" style="color: #003764;">

                                <div class="col-xs-4 col-sm-2 col-md-2" style="color: #003764; font-weight: bold;">
                                    <label>Usuario :</label>
                                </div>

                                <div class="col-xs-8 col-sm-4 col-md-4">
                                    <label id='usuario' for='usuario'></label>
                                </div>

                                <div class="col-xs-4 col-sm-2 col-md-2" style="color: #003764;">
                                    <label>Estado :</label>
                                </div>

                                <div class="col-xs-8 col-sm-4 col-md-4">
                                    <label id='estado' for='estado'></label>
                                </div>

                            </div>

                            <div class="row info" style="color: #003764;">
                                <div class="col-xs-4 col-sm-2 col-md-2" style="color: #003764;">
                                    <label>Nombre :</label>
                                </div>

                                <div class="col-xs-8 col-sm-10 col-md-10">
                                    <label id='nombre' for='nombre'></label>
                                </div>
                            </div>

                            <hr>
                            <div class="row info" style="padding-bottom: 1em;">
                                <div class="col-xs-4 col-sm-2 col-md-2" style="color: #003764;">
                                    <label>E-mail :</label>
                                </div>

                                <div class="col-xs-8 col-sm-10 col-md-10">
                                    <input type="mail" id='user_email' for='email' class="form-control input-sm" placeholder="E-mail" aria-describedby="sizing-addon3">
                                </div>

                            </div>
                            <div class="row info" style="padding-bottom: 1em;">
                                <div class="col-xs-4 col-sm-2 col-md-2" style="color: #003764;">
                                    <label>Password :</label>
                                </div>

                                <div class="col-xs-8 col-sm-10 col-md-10">
                                    <input type="text" id='user_password' for='password' class="form-control input-sm" placeholder="Password" aria-describedby="sizing-addon3">
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-dismissable alert-danger" id='div_error' hidden="hidden"></div>
                                    <div class="alert alert-dismissable alert-info" id='div_succesfull' hidden="hidden"></div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-xs-0 col-sm-2 col-md-2"></div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <button id="actualizar" class="btn btn-primary btn-sm" style="width:100%;"  onclick="save()">
                                        <i class="fa fa-repeat"></i> <?php echo 'Actualizar';?>
                                    </button>
                                </div>

                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <button id="send_email" class="btn btn-primary btn-sm" style="width:100%" onclick="sendEmail()">
                                        <i class="fa fa-envelope" aria-hidden="true"></i> <?php echo 'Envíar código de verificación' ?>
                                    </button>
                                </div>

                            </div>
                            <br><br>

                        </div>
                    </div>

                </div>
                <input id="iduser" name="iduser" type="hidden" value="">


                @endif




            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

    /**
     *  Funcion que permite buscar usuarios segun el permiso dado 
     * **/
    function BuscarUsuario() {

        $('#usuario').html('');
        $('#user_email').val('');
        $('#user_password').val('');
        $('#nombre').html('');
        $('#estado').html('');
        $('#div_error').hide('slow');
        $('#iduser').val('');
        $('#div_succesfull').hide('slow');
        $('#div_succesfull').html('');


        if ($('#user_input').val().length == 0) {
            $('#div_error').html(' -  Debe ingresar el usuario');
            $('#div_error').removeAttr('hidden');
            $('#div_error').show('slow');

        } else {
            $('#div_error').hide('slow');
            $('#div_error').html('');

            $.ajax({
                url: "{{ route('reset.getUser') }}",
                data: {'user': $('#user_input').val().trim()},
                type: 'GET'
                , success: function(response) {
                    if (response == 'false') {
                        $('#div_error').html(' -  Usuario no encontrado');
                        $('#div_error').removeAttr('hidden');
                        $('#div_error').show('slow');
                    } else {
                        let use = jQuery.parseJSON(response);
                        $('#usuario').html(use.username);
                        $('#user_email').val(use.email);
                        $('#nombre').html(use.name);
                        $('#iduser').val(use.id);
                        if (use.block == '0') {
                            $('#estado').html(' <i class="fa fa-check" aria-hidden="true"></i> ACTIVO');
                        } else {
                            $('#estado').html('<i class="fa fa-times" aria-hidden="true"></i> INACTIVO');
                        }
                    }
                }
                , statusCode: {
                    404: function() {
                        alert('Error 404 : web not found');
                    }
                }
                , error: function(xhr, status, error) {
                    //nos dara el error si es que hay alguno
                    alert(xhr.status);
                    //alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                    //console.log( JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                }
            });
        }
    }

    function save() {
       
            $('#div_error').hide('slow');
            $('#div_error').html('');
            $('#div_succesfull').hide('slow');
            $('#div_succesfull').html('');

            let flag = true;

            if ( $('#iduser').val().trim().length == 0 ){

                flag = false;
                $('#div_error').append('- Primero debe de buscar el usuario.   <br>');
                $('#div_error').removeAttr('hidden');
                $('#div_error').show('slow');

            } 

            if ( $('#user_input').val().length == 0 || $('#user_email').val().length == 0  || $('#user_password').val().length == 0 ) {
                flag = false;
                $('#div_error').append('- Los siguientes campos son obligarotirios:  <br> &nbsp;&nbsp;&nbsp;&nbsp;- Usuario <br> &nbsp;&nbsp;&nbsp;&nbsp;- E-mail <br> &nbsp;&nbsp;&nbsp;&nbsp;- Password <br> ');
                $('#div_error').removeAttr('hidden');
                $('#div_error').show('slow');
            }

            re=/^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/ 

            if ( !re.exec($('#user_email').val())  ){
                flag = false;
                $('#div_error').append('- E-mail no valido <br>');
                $('#div_error').removeAttr('hidden');
                $('#div_error').show('slow');
            }
            
            if ( $('#user_password').val().trim().length < 5 ){
                flag = false;
                $('#div_error').append('- El campo Contraseña debe contener al menos 5 carácteres.   <br>');
                $('#div_error').removeAttr('hidden');
                $('#div_error').show('slow');
            }
            
            
            if (flag){

                $.ajax({
                    url: "{{ route('reset.save') }}",
                    data: { 'id':$('#iduser').val().trim(), 'user': $('#user_input').val().trim(),'email':$('#user_email').val().trim(),'pass':$('#user_password').val()},
                    type: 'GET'
                    , success: function(response) {
                        if (response == 'false') {

                            $('#div_error').html(' - NO se puede realizar la actualizacion de contraseña para el usuario ' +$('#user_input').val().trim() + '.  El cambio se debe realizar en el directorio activo. ');
                            $('#div_error').removeAttr('hidden');
                            $('#div_error').show('slow');

                        } else {
                            $('#div_succesfull').html('- La contraseña del usuario   : ' +  $('#user_input').val() + ' se atualizo correctamente.');
                            $('#usuario').html('');
                            $('#user_email').val('');
                            $('#user_password').val('');
                            $('#nombre').html('');
                            $('#estado').html('');
                            $('#div_error').hide('slow');
                            $('#iduser').val('');
                            $('#user_input').val('');
                            $('#div_succesfull').removeAttr('hidden');
                            $('#div_succesfull').show('slow');  
                        }
                    }
                    , statusCode: {
                        404: function() {
                            alert('Error 404 : web not found');
                        }
                    }
                    , error: function(xhr, status, error) {
                        //nos dara el error si es que hay alguno
                        alert(xhr.status);
                        //alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                        //console.log( JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                    }
                });

        }
}
    function sendEmail() {

            
       $('#div_error').hide('slow');
       $('#div_error').html('');
       $('#div_succesfull').hide('slow');
       $('#div_succesfull').html('');
       

       let flag = true;

       if ( $('#iduser').val().trim().length == 0 ){

           flag = false;
           $('#div_error').append('- Primero debe de buscar el usuario.   <br>');
           $('#div_error').removeAttr('hidden');
           $('#div_error').show('slow');

       } 

       if (  $('#user_email').val().length == 0  ) {
           flag = false;
           $('#div_error').append('- Los siguientes campos son obligarotirios: <br> &nbsp;&nbsp;&nbsp;&nbsp;- E-mail <br> ');
           $('#div_error').removeAttr('hidden');
           $('#div_error').show('slow');
       }

       re=/^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/ 

       if ( !re.exec($('#user_email').val())  ){
           flag = false;
           $('#div_error').append('- E-mail no valido <br>');
           $('#div_error').removeAttr('hidden');
           $('#div_error').show('slow');
       }
       
    
       
       if (flag){

           $.ajax({
               url: "{{ route('reset.sendmail') }}",
               data: { 'id':$('#iduser').val().trim(), 'user': $('#user_input').val().trim(),'email':$('#user_email').val().trim(),'pass':$('#user_password').val()},
               type: 'GET'
               , success: function(response) {
                   if (response == 'false') {

                       $('#div_error').html(' - NO se puede realizar la actualizacion de contraseña para el usuario ' +$('#user_input').val().trim() + '. El cambio se debe realizar en el directorio activo. ');
                       $('#div_error').removeAttr('hidden');
                       $('#div_error').show('slow');

                   } else {
                       $('#div_succesfull').html('Se ha enviado un correo electrónico de restablecimiento de contraseña al usurio : <br><br> '+$('#user_input').val().trim()+'<br>' + $('#user_email').val().trim() +'. <br><br> Si no encuentras el correo en la bandeja de entrada, por favor revisar la carpeta de Spam. ');
                       $('#usuario').html('');
                       $('#user_email').val('');
                       $('#user_password').val('');
                       $('#nombre').html('');
                       $('#estado').html('');
                       $('#div_error').hide('slow');
                       $('#iduser').val('');
                       $('#user_input').val('');
                       $('#div_succesfull').removeAttr('hidden');
                       $('#div_succesfull').show('slow');  
                   }
               }
               , statusCode: {
                   404: function() {
                       alert('Error 404 : web not found');
                   }
               }
               , error: function(xhr, status, error) {
                   //nos dara el error si es que hay alguno
                   alert(xhr.status);
                   //alert('error: ' + JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
                   //console.log( JSON.stringify(x) +"\n error string: "+ xs + "\n error throwed: " + xt);
               }
           });

       }
   

    }




    

</script>

@endsection
