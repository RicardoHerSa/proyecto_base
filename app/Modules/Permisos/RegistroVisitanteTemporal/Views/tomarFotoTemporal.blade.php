@include('layouts.app', ['modulo' => 'temporal'])
    <div class="container mt-5">
        <div class="float-left">
            <a href="{{'../registro-visitante-temporal'}}" class="btn btn-primary">Vover</a>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-3 col-lg-3">
            </div>
            <div class="col-xs-12 col-md-6 col-lg-6">
                <div class="embed-responsive embed-responsive-4by3">
                    <video class="embed-responsive-item" id="webcam" autoplay></video>
                </div>
                <form id="form"  method="POST" class="mt-5" style="margin-left:140px">
                    <div class="">
                        <input class="btn btn-primary" type="button" id="screenshot-button" value="Capturar"/>
                        <input class="btn btn-danger" type="button" id="reset-button" value="Resetear"/>
                        <input class="btn btn-success" type="button" onclick="savePicture()" id="save-info" name="save-info" value="Aceptar"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
	

	<div style="display:none">
	<canvas id="canvas" ></canvas>
	</div>
	</div>
	<div class="col-md-3">
	</div>
</div>
<script type="text/javascript">
	var captura=false;
	var cedula= <?php echo $cedula; ?>;
	
        function onFailure(err) {
            //alert("The following error occured: " + err.name);
        window.alert("Se debe permitir el uso de la camara");
		}
		var video;
            var button;
			var button2 ;
			//var btn_aceptar=document.querySelector('#save-info');
            var canvas ;
			var ctx;
        $(document).ready(function () {
            video = document.querySelector('#webcam');
           button = document.querySelector('#screenshot-button');
			 button2 = document.querySelector('#reset-button');
			//var btn_aceptar=document.querySelector('#save-info');
          canvas = document.querySelector('#canvas');
            ctx = canvas.getContext('2d');

            navigator.getUserMedia = (navigator.getUserMedia ||
                            navigator.webkitGetUserMedia ||
                            navigator.mozGetUserMedia ||
                            navigator.msGetUserMedia);
            if (navigator.getUserMedia) {
                navigator.getUserMedia
                            (
                              { video: true },
                              function (localMediaStream) {
                              	    try {
					 video.srcObject = localMediaStream;
					} catch (error) {
					video.src = window.URL.createObjectURL(localMediaStream);
}
                              	    video.srcObject = localMediaStream;
                              //    video.src = window.URL.createObjectURL(localMediaStream);
                              }, onFailure);
            }
            else {
                onFailure();
            }
            button.addEventListener('click',snapshot, false);
			button2.addEventListener('click',reset, false);
			//btn_aceptar.addEventListener('click',savePicture,false);
        });

		function reset(){
		document.getElementById('webcam').play();
		}
		 function snapshot() {
                canvas.width = 400;//video.videoWidth;
                canvas.height = 300;//video.videoHeight;
                ctx.drawImage(video, 0, 0,400,300);
				captura=true;
				document.getElementById('webcam').pause();
            }
		function savePicture(){
					if(captura){
							exportAndSaveCanvas(cedula);
						}else{
							snapshot();
							exportAndSaveCanvas(cedula);
						}
				captura=false;
				
			}
		
	function exportAndSaveCanvas(id_archivo)  {
//window.alert(id_archivo);
		// Get the canvas screenshot as PNG
		var screenshot = Canvas2Image.saveAsPNG(canvas, true);
		//alert("User: "+username);
		// This is a little trick to get the SRC attribute from the generated <img> screenshot
		canvas.parentNode.appendChild(screenshot);
		screenshot.id = "canvasimage";		
		data = $('#canvasimage').attr('src');
		canvas.parentNode.removeChild(screenshot);


		// Send the screenshot to PHP to save it on the server
		var url = 'guardarFoto';
		var request= $.ajax({ 
		    //type: "POST", 
		type: 'GET',
        async: false,
        cache: false,
        timeout: 30000,
        error: function(){
           console.log("no "+msg);
        },
        success: function(msg){ 
            console.log("si "+msg);
        },
		    url: url,
		    dataType: 'text',
		    data: {
		        base64data : data,
				nombre_archivo: id_archivo
		    }
		});
	}
    </script>

    @include('layouts.footer', ['modulo' => 'asignacion'])