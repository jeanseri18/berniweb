@extends('layouts.app')

@section('content')

<section class="contact-intro">
    <div class="contact-intro-inner">
      <center>  <h2 class="contact-title">Nous sommes là pour vous répondre</h2>
        <p class="contact-lead">Chez BERRNI, derrière la technologie, il y a des personnes.<br>
            Que ce soit pour une question, un besoin d’information, une suggestion ou une demande
            d’assistance, nous restons à l’écoute.</p></center>
    </div>
</section>

<section class="contact-cards">
    <div class="notice-card">
        <div class="circle-icon">
            <i class="bi bi-life-preserver"></i>
        </div>
        <div class="notice-text">
            <p>Pour une urgence liée à un colis en cours, merci d’utiliser
            l’<strong>application mobile BERRNI</strong> et la rubrique <strong>SOS colis</strong>.</p>
        </div>
    </div>

    <div class="whatsapp-card">
        <div class="wh-text">
            <p>Pour une question simple ou une demande d’information rapide,
            vous pouvez aussi contacter l’équipe BERRNI directement sur <strong>WhatsApp</strong>.</p>
        </div>
        <a class="wh-btn" href="#" aria-label="Contacter sur WhatsApp">
            <i class="bi bi-whatsapp"></i>
            <span>Cliquez ici</span>
        </a>
    </div>
</section>

<section class="contact-form-wrap">
    <div class="contact-form-card">
        <h3 class="form-title">Envoyez-nous un message</h3>
        <form class="contact-form" action="#" method="post">
            <div class="form-row">
                <input type="text" name="name" placeholder="Nom et prénom" required>
            </div>
            <div class="form-row">
                <input type="email" name="email" placeholder="Adresse email" required>
            </div>
            <div class="form-row">
                <input type="text" name="subject" placeholder="Objet du message" required>
            </div>
            <div class="form-row">
                <textarea name="message" rows="5" placeholder="Message" required></textarea>
            </div>
            <button type="submit" class="btn-send">Envoyer le message</button>
            <p class="form-note">Nous répondons dans les meilleurs délais.</p>
        </form>
    </div>
</section>

<section class="social-follow">
    <p>Suivez-nous sur :
        <a href="#" class="social-link" aria-label="Site"><i class="bi bi-globe2"></i></a>
        <a href="#" class="social-link" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" class="social-link" aria-label="Musique"><i class="bi bi-music-note-beamed"></i></a>
    </p>
</section>

<section class="contact-cta" style="background-image:url('{{ asset('Image 3.png') }}')">
    <div class="overlay"></div>
    <div class="cta-inner">
        <h3 class="cta-title">Prêt à essayer BERRNI ?</h3>
        <p class="cta-desc">Téléchargez l’application dès maintenant et envoyez votre colis<br>
            en toute confiance !</p>
        <a class="cta" href="#telecharger">
            <span class="icon">⬇</span>
            <span>Télécharger l’application</span>
        </a>
    </div>
</section>

@endsection