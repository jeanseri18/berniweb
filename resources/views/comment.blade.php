@extends('layouts.app')

@section('content')
<section class="hero" style="background-image:url('{{ asset('Image 1.png') }}')">
    <div class="overlay"></div>
    <div class="hero-inner">
        <h1 class="hero-title" style="color:white !important;">BERRNI, la confiance<br>voyage avec vous.</h1>
    </div>
</section>
<section id="comment" class="steps">
    <div class="steps-header">
        <h2>Comment ça marche ?</h2>
        <p>Envoyez un colis. Quelqu’un de confiance le transporte.<br>Simplement. Humainement. Sécurisé.</p>
    </div>

    <div class="steps-grid">
        <article class="step-card">
            <div class="step-number">01</div>
            <h3>Vous publiez votre colis</h3>
            <p>Sur BERRNI, tout commence simplement. 
Vous décrivez votre colis, indiquez la ville 
de départ et d’arrivée, puis proposez une 
rémunération libre et juste. <br>
Cette rémunération est sécurisée : 
elle est bloquée temporairement pour 
protéger tout le monde jusqu’à la livraison.</p>
        </article>

        <article class="step-card">
            <div class="step-number">02</div>
            <h3>Un relais de confiance accepte le trajet</h3>
            <p>Une personne qui effectue déjà le trajet — 
en bus, train, voiture ou avion — propose 
de transporter votre colis.<br> 
Vous échangez dans un cadre encadré, avec 
une négociation limitée pour rester claire, 
respectueuse et efficace.  <br>
Ce n’est pas un livreur anonyme, c’est une 
personne de confiance.</p>
        </article>

        <article class="step-card">
            <div class="step-number">03</div>
            <br>
            <h3>Vous suivez le colis, étape par étape</h3>
            <p>Dès que le relais est confirmé, vous suivez 
le colis tout au long de son trajet. <br>
Chaque action est tracée, chaque étape est 
claire. <br>
BERRNI reste présent pour accompagner, 
notifier et rassurer. <br>
Pas de zones floues. <br>
Tout est transparent.</p>
        </article>
    </div>
<br><br><br>
    <div class="steps-footer">
        <div class="step-card wide">
            <div class="step-number">04</div>
            <h3>Livraison confirmée, paiement libéré</h3>
            <p>Une fois le colis remis au destinataire, la livraison est confirmée.</p>
            <p>Le relais est automatiquement rémunéré, sans discussion ni stress.</p>
            <p>En cas de souci, le service SOS Colis permet une médiation humaine et rapide.</p>
            <p class="tagline">Chez BERRNI, la parole donnée a de la valeur.</p>
        </div>
    </div>


</section>
    <div class="steps-cta">
        <p>Prêt à essayer BERRNI ? Rejoignez la communauté et envoyez votre colis en toute confiance.</p>
        <a class="cta" href="#telecharger"><span class="icon">⬇</span><span>Télécharger l’application</span></a>
    </div>
@endsection