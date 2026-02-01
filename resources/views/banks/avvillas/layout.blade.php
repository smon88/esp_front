<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- CSS del banco --}}
    <link rel="stylesheet" href="{{ asset('assets/css/payment/avvillas.css') }}">

    {{-- Bootstrap / Fonts / Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>@yield('title', 'Secure Payment')</title>

    <script type="text/javascript" src="{{asset('assets/js/jquery-3.6.0.min.js')}}"></script>

    {{-- Para CSS extra por pantalla --}}
    @stack('head')
</head>

<body>

    {{-- ALERT (se puede mostrar/ocultar por pantalla) --}}
    @yield('alert')

    <div id="page">
        <div class="contenido">
            @yield('content')
        </div>
    </div>

 @stack('scripts')

</body>
</html>
