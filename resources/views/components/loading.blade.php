@props([
  'id' => 'globalLoading',
  'text' => 'Estamos procesando tu transacción...',
  'show' => false,
])

<div id="{{ $id }}" class="processing-container {{ $show ? '' : 'hidden' }}">
    <h2 id="nameUser" hidden></h2>
    <!-- <p>{{ $text }}</p> -->
    <div class="logo-container">
        <img src="{{ asset('assets/img/procesando.gif') }}" alt="Procesando">
    </div>
</div>