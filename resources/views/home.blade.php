@extends('layouts.app')

@section('content')
<section class="hero" style="background-image:url('{{ asset('Image 2.png') }}')">
    <div class="overlay"></div>
    <div class="hero-inner">
        <h1 class="hero-title" style="color:white !important;">La confiance voyage<br>avec BERRNI.</h1>
        <p class="hero-sub" style="color:white !important;">Envoyez un colis. Quelqu’un de confiance le transporte.<br>
            Simplement. Humainement. Sécurisé.</p>
        <a class="cta" href="#telecharger">
            <i class="bi bi-download icon" aria-hidden="true"></i>
            <span>Télécharger BERRNI</span>
        </a>
    </div>
</section>
@endsection
