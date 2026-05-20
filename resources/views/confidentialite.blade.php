@extends('layouts.app')

@section('content')
<section class="cgu-header">
  <div class="container">
    <h1 class="cgu-title">Politique de confidentialité — BERRNI</h1>
    <p class="cgu-lead">La présente politique décrit comment BERRNI collecte, utilise, conserve et protège vos données personnelles lorsque vous utilisez l’application mobile et le site web. Elle s’applique à tous les utilisateurs : expéditeurs, relais de confiance et visiteurs.</p>
    <p class="cgu-lead" style="margin-top:0.75rem;font-size:0.95rem;opacity:0.9;">Dernière mise à jour : {{ date('d/m/Y') }}</p>
  </div>
</section>

<section class="cgu-body">
  <div class="cgu-container">
    <article class="cgu-section">
      <h3>1. Responsable du traitement</h3>
      <p>Le responsable du traitement des données est <strong>BERRNI</strong>, éditeur de la plateforme de mise en relation pour le transport de colis entre particuliers. Pour toute question relative à vos données : <a href="{{ url('/contact') }}">page Contact</a>.</p>
    </article>

    <article class="cgu-section">
      <h3>2. Données collectées</h3>
      <p>Selon votre utilisation, nous pouvons traiter :</p>
      <ul class="checklist">
        <li>identité et coordonnées (nom, téléphone, e-mail) ;</li>
        <li>données de compte et de vérification (OTP, statut relais) ;</li>
        <li>documents KYC (pièce d’identité, selfies) pour les relais de confiance ;</li>
        <li>informations sur les colis (trajet, description, photos) ;</li>
        <li>messages échangés via la messagerie intégrée ;</li>
        <li>données de paiement et portefeuille (transactions, séquestre) ;</li>
        <li>données techniques (logs, appareil, adresse IP) pour la sécurité du service.</li>
      </ul>
    </article>

    <article class="cgu-section">
      <h3>3. Finalités du traitement</h3>
      <ul class="checklist">
        <li>création et gestion de votre compte ;</li>
        <li>mise en relation expéditeur / relais et suivi des livraisons ;</li>
        <li>sécurisation des paiements et lutte contre la fraude ;</li>
        <li>vérification d’identité des relais (KYC) ;</li>
        <li>assistance, médiation (SOS Colis) et support client ;</li>
        <li>amélioration du service et statistiques agrégées ;</li>
        <li>respect des obligations légales.</li>
      </ul>
    </article>

    <article class="cgu-section">
      <h3>4. Base légale</h3>
      <p>Le traitement repose notamment sur : l’exécution du contrat (utilisation de BERRNI), votre consentement (inscription, documents KYC, notifications), l’intérêt légitime (sécurité, amélioration du service) et les obligations légales applicables.</p>
    </article>

    <article class="cgu-section">
      <h3>5. Durée de conservation</h3>
      <p>Les données sont conservées pendant la durée nécessaire aux finalités, puis archivées ou supprimées selon les délais légaux. Les documents KYC et les traces de transactions peuvent être conservés plus longtemps en cas d’obligation réglementaire ou de litige.</p>
    </article>

    <article class="cgu-section">
      <h3>6. Partage des données</h3>
      <p>Vos données ne sont pas vendues. Elles peuvent être communiquées :</p>
      <ul class="checklist">
        <li>à l’autre partie d’une course (dans la limite du nécessaire à la livraison) ;</li>
        <li>à nos prestataires techniques (hébergement, paiement) sous contrat de confidentialité ;</li>
        <li>aux autorités compétentes si la loi l’exige.</li>
      </ul>
    </article>

    <article class="cgu-section">
      <h3>7. Sécurité</h3>
      <p>BERRNI met en œuvre des mesures organisationnelles et techniques (chiffrement des échanges, authentification, accès restreint, surveillance) pour protéger vos données contre l’accès non autorisé, la perte ou l’altération.</p>
    </article>

    <article class="cgu-section">
      <h3>8. Vos droits</h3>
      <p>Selon la réglementation applicable, vous disposez notamment des droits d’accès, de rectification, d’effacement, de limitation, d’opposition et de portabilité. Pour les exercer, contactez-nous via la <a href="{{ url('/contact') }}">page Contact</a>. Vous pouvez également introduire une réclamation auprès de l’autorité de protection des données compétente.</p>
    </article>

    <article class="cgu-section">
      <h3>9. Application et site web</h3>
      <p>Le site peut utiliser des cookies ou traceurs techniques strictement nécessaires au fonctionnement. Aucune publicité ciblée tierce n’est déployée sans votre consentement.</p>
    </article>

    <article class="cgu-section">
      <h3>10. Modifications</h3>
      <p>Cette politique peut être mise à jour. En cas de changement important, vous serez informé via l’application ou le site. La poursuite de l’utilisation après notification vaut prise de connaissance.</p>
    </article>

    <article class="cgu-section">
      <h3>11. Liens utiles</h3>
      <p>Consultez également nos <a href="{{ url('/cgu') }}">Conditions générales d’utilisation</a>.</p>
    </article>
  </div>
</section>

<section class="cta-banner">
  <div class="cta-inner">
    <h3 class="cta-title">Des questions sur vos données ?</h3>
    <p>Notre équipe est disponible pour vous répondre.</p>
    <a class="cta-btn" href="{{ url('/contact') }}">Nous contacter</a>
  </div>
</section>
@endsection
