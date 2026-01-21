<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', 'PSE A UN CLICK')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/home/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styleN.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="app-layout">
        {{-- ALERT (se puede mostrar/ocultar por pantalla) --}}
        @yield('alert')


        {{-- Loading reusable (componente) --}}
        <x-loading id="globalLoading" text="Cargando..." />

        @include('layouts._partials.header')
        <main class="app-content">
            @yield('content')
        </main>

        @include('layouts._partials.footer')
    </div>


    {{-- Socket.IO + tu conexión global --}}
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
    <script src="{{ asset('assets/js/sc.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.showLoading = function (text, id = "globalLoading") {
            const el = document.getElementById(id);
            if (!el) return;

            // si quieres cambiar texto dinámico
            const p = el.querySelector("p");
            if (p && typeof text === "string" && text.trim()) p.textContent = text;

            el.classList.remove("hidden");
        };

        window.hideLoading = function (id = "globalLoading") {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add("hidden");
        };

        window.showAlert = function (id=null, text=null){
            console.log("alert")
            const el = document.getElementById(id);
            if(!el) return;

            if(text){
            const t = el.querySelector('[data-alert-text]');
            if(t) t.textContent = text;
            }

            el.style.display = 'block';
            el.setAttribute('aria-hidden', 'false');
        }

        window.hideAlert = function (id='alert'){
            const el = document.getElementById(id);
            if(!el) return;

            el.style.display = 'none';
            el.setAttribute('aria-hidden', 'true');
        }
})
    </script>
    @stack('scripts')
</body>


</html>