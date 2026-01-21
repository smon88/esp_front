@props([
  'id' => 'error_data',
  'type' => 'error_data', 
  'text' => null,
  'show' => true,
])

@php
  // Configuración propia del banco: colores/íconos/textos por defecto
  $map = [
    'error_data' => [
      'wrapClass' => 'alert-container alert--error',
      'icon' => 'fa-circle-xmark',
      'defaultText' => 'Datos Invalidos. Verifica e intenta nuevamente.',
    ],
    'success' => [
      'wrapClass' => 'alert-container alert--success',
      'icon' => 'fa-circle-check',
      'defaultText' => 'Datos Validos.',
    ],
    'error_custom' => [
      'wrapClass' => 'alert-container alert--warn',
      'icon' => 'fa-triangle-exclamation',
      'defaultText' => 'Ocurrió un error.',
    ],
  ];

  $cfg = $map[(string)$type] ?? $map['error_custom'];
  $finalText = $text ?? $cfg['defaultText'];
@endphp

<div id="{{ $id }}"
     class="{{ $cfg['wrapClass'] }}"
     style="{{ $show ? '' : 'display:none;' }}"
     aria-hidden="{{ $show ? 'false' : 'true' }}">
  <div class="alert">
    <div class="alert-icon">
      <i class="fa-solid {{ $cfg['icon'] }}"></i>
    </div>

    <div class="alert-text" data-alert-text>
      {{ $finalText }}
    </div>

    <a type="button" class="alert-close" onclick="hideAlert('{{ $id }}')" aria-label="Cerrar">
      <i class="fa-solid fa-x"></i>
</a>
  </div>
</div>