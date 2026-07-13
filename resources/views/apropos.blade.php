@extends('layouts.app')

@section('content')
<section class="about about-intro">
    <div class="container">
        <h1 class="about-title">À propos de BERRNI</h1>
        <h3 class="about-sub">Une nouvelle façon de faire voyager la confiance</h3>
        <p class="about-lead">BERRNI est une plateforme numérique de transport collaboratif de colis entre villes, pensée pour faciliter les échanges entre particuliers, de manière simple, humaine et sécurisée.</p>
        <p class="about-lead">Dans de nombreux pays africains, envoyer un colis d’une ville à une autre repose encore sur des solutions coûteuses, informelles ou peu transparentes. BERRNI est née d’un constat simple : <strong>des milliers de personnes voyagent chaque jour entre les villes</strong>, pendant que d’autres cherchent un moyen fiable d’envoyer un colis.</p>
        <p class="about-link"><a href="#">BERRNI crée le lien.</a></p>
    </div>
</section>


<section class="about about-vision">
    <div class="container">
        <h2 class="about-section-title accent">Notre vision</h2>
        <p class="about-note">Créer une plateforme de référence pour le transport collaboratif de colis en Afrique, où la technologie renforce la confiance entre les personnes, et où chaque trajet devient une opportunité de rendre service.</p>

        <div class="about-grid">
            <article class="about-card">
                <div class="about-icon" aria-hidden="true">
                    <img src="{{ asset('faq/colis.jpg') }}" alt="Expéditeur BERRNI" style="width: 88px; height: 88px; border-radius: 16px; object-fit: cover;">
                </div>
                <h3>Expéditeur</h3>
                <p>Ceux qui veulent envoyer un colis sans stress.</p>
            </article>
            <article class="about-card">
                <div class="about-icon" aria-hidden="true">
                    <img src="{{ asset('faq/livreumoto.jpg') }}" alt="Relais de confiance BERRNI" style="width: 88px; height: 88px; border-radius: 16px; object-fit: cover;">
                </div>
                <h3>Relais de confiance</h3>
                <p>Ceux qui voyagent et qui souhaitent rendre service, <strong>en étant payé.</strong></p>
            </article>
            <article class="about-card">
                <div class="about-icon" aria-hidden="true">
                    <img src="{{ asset('faq/community.jpg') }}" alt="Communauté locale BERRNI" style="width: 88px; height: 88px; border-radius: 16px; object-fit: cover;">
                </div>
                <h3>Communauté locale</h3>
                <p>Où la confiance est essentielle.</p>
            </article>
        </div>

        <h2 class="about-section-title accent">Une plateforme centrée sur l’humain</h2>
        <div class="about-human">
            <p>Contrairement aux services de livraison classiques, BERRNI ne remplace pas l’humain : <strong>elle le structure, l’accompagne et le sécurise.</strong></p>
            <p>Chaque colis est confié à une personne réelle, identifiée, engagée et encadrée par des règles claires.</p>
            <p>La plateforme favorise la confiance, la parole donnée et la responsabilité partagée.</p>
            <p class="about-tagline"><em>Chez BERRNI, ce ne sont pas des colis qui voyagent seuls,<br>ce sont des relations de confiance qui se construisent.</em></p>
        </div>
    </div>
</section>

<section class="about about-columns">
    <div class="container two-cols">
        <div class="col">
            <h3 class="col-title">Sécurité, transparence et responsabilité</h3>
            <ul class="checklist">
                <li>Paiement sous séquestre, libéré uniquement après livraison,</li>
                <li>Messagerie encadrée entre expéditeur et relais,</li>
                <li>Suivi du colis à chaque étape,</li>
                <li>Service SOS Colis en cas d’incident ou de litige.</li>
            </ul>
            <p>Chaque action est tracée, chaque étape est visible.</p>
        </div>
        <div class="col">
            <h3 class="col-title">Une solution pensée pour l’Afrique</h3>
            <p>BERRNI est conçue pour répondre aux réalités des déplacements intercity en Afrique.</p>
            <p>Elle valorise les trajets existants, réduit les coûts logistiques et favorise une économie plus collaborative, responsable et durable.</p>
            <p>Scalable par nature, BERRNI a vocation à évoluer progressivement, en ajoutant de nouveaux services à mesure que la confiance et l’usage grandissent.</p>
        </div>
    </div>
</section>

<div class="steps-cta">
    <p>Prêt à essayer BERRNI ?<br>Téléchargez l’application dès maintenant et envoyez votre colis en toute confiance !</p>
    <a class="cta" href="#telecharger"><span class="icon">⬇</span><span>Télécharger l’application</span></a>
</div>
@endsection
