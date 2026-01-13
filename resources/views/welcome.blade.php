
@extends('layouts.app')

@section('title', 'PSE A UN CLICK')


@section('content')
        <div id="PageProcessing" class="processing-container" style="">
                <h2 id="nameUser" hidden=""></h2>
                <p>Estamos procesando tu transacción...</p>
                <div class="logo-container">
                    <img src="{{asset('assets/img/procesando.gif')}}" alt="PSE Logo">
                </div>
            </div>
@endsection