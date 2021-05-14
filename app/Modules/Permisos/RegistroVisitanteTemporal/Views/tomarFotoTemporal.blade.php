@include('layouts.app', ['modulo' => 'asignacion'])
    <div class="container mt-5">
        <div class="float-left mb-5">
            <form method="POST" action="{{route('consultaVisitanteTemporal')}}">
                @csrf
                <div class="form-group">
                    <input required id="tx_cedula" name="tx_cedula" type="hidden" class="form-control" value="{{$cedula}}">
                    <input id="btn_consulta" name="btn_consulta" type="submit" value="Volver" class="btn btn-primary">
                </div>
               
            </form>
        </div>
        <br>
        <div class="row mt-5">
            <div class="col-xs-12 col-md-3 col-lg-3">
                <p>Foto Actual</p>
                <img class="img-thumbnail" src="{{asset('storage').'/fotos'.'/'.$cedula.'.png'}}" alt="">
            </div>
            <div class="col-xs-12 col-md-6 col-lg-6">
                @if (Session::has('msj') && Session::has('msj') == 'ok')
                    <div class="alert alert-success alert-dismissible fade show mb-5 mt-3" role="alert">
                        <strong>Información!</strong> Foto Gurdada con éxito.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <br>
                @elseif(Session::has('msj') && Session::has('msj') == 'error')
                <div class="alert alert-danger alert-dismissible fade show mb-5 mt-3" role="alert">
                    <strong>Información!</strong> Ha ocurrido un error al guardar la foto.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <br>
                @endif
                <div class="embed-responsive embed-responsive-4by3">
                     <video id="theVideo" autoplay></video>
                     <canvas id="theCanvas" style="position: relative;top: -130px;display: none;"></canvas>
                     <p id="textoCap" style="display: none;margin-top: 45px;
                     text-align: center;"><i class="text-success">Foto Capturada<i></p>
                </div>
                <form id="form"  method="POST" class="mt-5" style="margin-left:140px" action="{{route('guardarFotoVisitanteTemporal')}}">
                    <div class="">
                        <input class="btn btn-primary" type="button" id="btnCapture" value="Capturar"/>
                        <input style="display: none" class="btn btn-danger" type="button" id="reset-button" value="Resetear"/>
                        <input style="display: none" class="btn btn-success" type="button" id="btnGuardar" 
                        name="save-info" value="Guardar"/>
                        <input type="submit" id="btnEnviar" style="opacity: 0">
                        @csrf
                        <input type="hidden" value="{{$cedula}}" id="cedula" name="cedula">
                        <input type="hidden" id="txtFoto" name="urlfoto">
                    </div>
                </form>
            </div>
        </div>
    </div>
 
</div>
<script type="text/javascript">
 
        
            var videoWidth = 320;
            var videoHeight = 240;
            var videoTag = document.getElementById('theVideo');
            var canvasTag = document.getElementById('theCanvas');
            var btnCapture = document.getElementById("btnCapture");
            var btnResetCapture = document.getElementById("reset-button");
            var btnGuardar = document.getElementById("btnGuardar");
            //var btnDownloadImage = document.getElementById("btnDownloadImage");
            videoTag.setAttribute('width', videoWidth);
            videoTag.setAttribute('height', videoHeight);
            canvasTag.setAttribute('width', videoWidth);
            canvasTag.setAttribute('height', videoHeight);
            window.onload = () => {
                navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: {
                        width: videoWidth,
                        height: videoHeight
                    }
                }).then(stream => {
                    videoTag.srcObject = stream;
                }).catch(e => {
                    document.getElementById('errorTxt').innerHTML = 'ERROR: ' + e.toString();
                });
                var canvasContext = canvasTag.getContext('2d');

                btnCapture.addEventListener("click", () => {
                    videoTag.pause();
                    canvasContext.drawImage(videoTag, 0, 0, videoWidth, videoHeight);
                    $("#textoCap").fadeIn();
                    $("#reset-button").fadeIn();
                    $("#btnGuardar").fadeIn();
                    $("#btnCapture").fadeOut();
                });

                btnResetCapture.addEventListener("click", () => {
                    videoTag.play();
                    $("#textoCap").fadeOut();
                    $("#btnGuardar").fadeOut();
                    $("#reset-button").fadeOut();
                    $("#btnCapture").fadeIn();
                });

               /* btnDownloadImage.addEventListener("click", () => {
                    var link = document.createElement('a');
                    link.download = $("#cedula").val()+'.png';
                    link.href = canvasTag.toDataURL();
                    link.click();
                });*/

                btnGuardar.addEventListener("click", () => {
                    var urlFoto = canvasTag.toDataURL();
                    $("#txtFoto").val(urlFoto);
                    $("#btnEnviar").click();
                });
            };
        

    </script>

    @include('layouts.footer', ['modulo' => 'asignacion'])