@extends('layouts.app')

@section('title', 'Autorizas a PSE')

@section('content')
  <div class="step-process">
    <img class="step-process-img" src="{{ asset('assets/img/home/step-process-1-mobile.svg') }}">
  </div>

  <div class="step-content">
    <div class="authorize-card">
      <div class="autorize-img">
        <img class="auth-form-img" src="{{ asset('assets/img/home/autoriza.svg') }}">
      </div>

      <div class="autorize-form">
        <div class="form-element">
          <input id="tdp" type="checkbox" style="appearance:auto;" name="tdp">
          <label for="tdp">Acepto el tratamiento de datos personales.</label>
        </div>

        <div class="form-element">
          <input id="tyc" type="checkbox" style="appearance:auto;" name="tyc">
          <label for="tyc">Acepto los términos y condiciones.</label>
        </div>

        <div class="btn-container">
          <button class="btn-continue" id="btnStep1" type="button">Continuar</button>
          <button class="btn-return" type="button">Regresar al Comercio</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById("btnStep1");
  const tdp = document.getElementById("tdp");
  const tyc = document.getElementById("tyc");

  if (!btn) return;

  btn.addEventListener('click', () => {
    // opcional: valida checks
    if (!tdp?.checked || !tyc?.checked) {
      window.showBankAlert?.("error_custom", "Debes aceptar los términos para continuar.");
      return;
    }

    window.location.href = "{{ route('pago.step2') }}";
  });
});
</script>
@endpush
