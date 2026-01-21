@extends('layouts.app')

@section('title', 'Error de conexión')

@section('content')
    <div class="finish-container">
        <div class="finish-main-card">
            <div class="main-card-container">
                <div class="main-card-text">
                    <h1>Tu Pago está en proceso</h1>
                    <span>
                        La confirmación de tu pago puede demorar
                        hasta 48 Hs.
                    </span>
                </div>
                <div class="main-card-logo">
                    <div class="finish-logo">
                        <img class="finish-logo" src="{{ asset('assets/img/home/default-logo.png') }}">
                    </div>
                    <div class="finish-logo-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0 8C0 3.58172 3.58172 0 8 0C12.4183 0 16 3.58172 16 8C16 12.4183 12.4183 16 8 16C3.58172 16 0 12.4183 0 8Z"
                                fill="#FF7733"></path>
                            <path d="M8.96967 3.87891H7.03027L7.2727 9.21224H8.72724L8.96967 3.87891Z" fill="white"></path>
                            <path
                                d="M7.99997 10.1819C8.53552 10.1819 8.96967 10.6161 8.96967 11.1516C8.96967 11.6872 8.53552 12.1213 7.99997 12.1213C7.46442 12.1213 7.03027 11.6872 7.03027 11.1516C7.03027 10.6161 7.46442 10.1819 7.99997 10.1819Z"
                                fill="white"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="finish-reference-card">
            <div class="payment-reference">
                <div class="reference-icon">
                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.9793 20.75H15.4753V19.25H19.9793V20.75Z" fill="#000000" fill-opacity="0.9">
                        </path>
                        <path d="M15.4743 17.75H24.4773V16.25H15.4743V17.75Z" fill="#000000" fill-opacity="0.9">
                        </path>
                        <path d="M24.4773 14.75H15.4743V13.25H24.4773V14.75Z" fill="#000000" fill-opacity="0.9">
                        </path>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M14.9923 31.5138L11.751 28.5659L11.7531 8.75H28.1985V28.566L24.9573 31.5138L22.466 29.2481L19.9748 31.5138L17.4835 29.2481L14.9923 31.5138ZM13.251 27.9026L14.9923 29.4862L17.4835 27.2205L19.9748 29.4862L22.466 27.2205L24.9573 29.4862L26.6985 27.9026V10.25H13.2529L13.251 27.9026Z"
                            fill="#000000" fill-opacity="0.9"></path>
                        <rect x="0.5" y="0.5" width="39" height="39" rx="19.5" stroke="black" stroke-opacity="0.07">
                        </rect>
                    </svg>
                </div>
                <div class="reference-text">
                    <span style="color: rgba(0, 0, 0, .55);">Referencia de pago (CUS)</span>
                    <span style="color: rgba(0, 0, 0, .9);">2100692130</span>
                </div>
            </div>
        </div>
        <div class="finish-detail-card">
            <div class="payment-date">
                <span style="color: rgba(0, 0, 0, .55);">Fecha de pago</span>
                <span style="color: rgba(0, 0, 0, .9);">{{ now()->locale('es')->translatedFormat('d/M/Y') }}</span>
            </div>
            <div class="payment-details">
                <span style="color: rgba(0, 0, 0, .55);">Detalle de pago</span>
                <span class="commerce" style="color: rgba(0, 0, 0, .9);">Compra en ZENTRA | Tienda Oficial Colombia</span>
                <span style="color: rgba(0, 0, 0, .9);">PSE</span>
                <span style="color: rgba(0, 0, 0, .9);">$ 570.000</span>
            </div>
        </div>

        <div class="foot-alert-card">
            <div class="alert-text">
                <span style="color: rgba(0, 0, 0, .9);">Si aún no realizaste el pago, abandona esta página y vuelve a hacer tu compra..</span>
            </div>
        </div>
    </div>
@endsection