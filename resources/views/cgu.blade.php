@extends('layouts.app')

@section('content')
<section class="cgu-header">
  <div class="container">
    <h1 class="cgu-title">Conditions Générales d’Utilisation (CGU) — BERRNI</h1>
    <p class="cgu-lead">Les Conditions Générales d’Utilisation (CGU) de BERRNI définissent le cadre légal, les règles d’usage et les responsabilités applicables à l’ensemble des utilisateurs de la plateforme, qu’ils soient expéditeurs, relais de confiance ou simples visiteurs. Elles ont pour objectif de garantir un environnement sécurisé, transparent et fondé sur la confiance, en cohérence avec la mission de BERRNI : faciliter le transport de colis intercity entre particuliers, de manière humaine et responsable.</p>
  </div>
</section>

<section class="cgu-body">
  <div class="cgu-container">
    <article class="cgu-section">
      <h3>1. Objet des CGU</h3>
      <p>Les présentes CGU ont pour objet de définir :</p>
      <ul class="checklist">
        <li>les conditions d’accès et d’utilisation de l’application et du site BERRNI,</li>
        <li>les droits et obligations des utilisateurs,</li>
        <li>les règles encadrant les services proposés,</li>
        <li>les limites de responsabilité de la plateforme.</li>
      </ul>
      <p>Toute utilisation de BERRNI implique l’acceptation pleine et entière des présentes CGU.</p>
    </article>

    <article class="cgu-section">
      <h3>2. Description du service BERRNI</h3>
      <p>BERRNI est une plateforme numérique de mise en relation entre particuliers permettant :</p>
      <ul class="checklist">
        <li>à un expéditeur de confier un colis,</li>
        <li>à un relais de confiance (voyageur) de transporter ce colis sur un trajet intercity existant.</li>
      </ul>
      <p>BERRNI n’est ni un transporteur professionnel, ni un service de messagerie classique. La plateforme agit comme intermédiaire de confiance, en assurant la traçabilité, la sécurisation des paiements et la communication entre les parties.</p>
    </article>

    <article class="cgu-section">
      <h3>3. Accès à la plateforme</h3>
      <p>L’accès aux services BERRNI nécessite :</p>
      <ul class="checklist">
        <li>le téléchargement de l’application mobile officielle,</li>
        <li>la création d’un compte utilisateur,</li>
        <li>la fourniture d’informations exactes et vérifiables.</li>
      </ul>
      <p>BERRNI se réserve le droit de refuser ou suspendre l’accès à tout utilisateur ne respectant pas les CGU.</p>
    </article>

    <article class="cgu-section">
      <h3>4. Rôles et responsabilités des utilisateurs</h3>
      <h4>Expéditeur</h4>
      <ul class="checklist">
        <li>Déclare de manière honnête le contenu du colis,</li>
        <li>Respecte la liste des objets interdits,</li>
        <li>Fixe une rémunération équitable,</li>
        <li>Confirme la réception du colis à l’arrivée.</li>
      </ul>
      <h4>Relais de confiance</h4>
      <ul class="checklist">
        <li>Transporte le colis avec soin,</li>
        <li>Respecte le trajet déclaré,</li>
        <li>Remet le colis au bon destinataire,</li>
        <li>Respecte la confidentialité et la sécurité du colis.</li>
      </ul>
      <p>Chaque utilisateur est responsable de ses actes et engagements.</p>
    </article>

    <article class="cgu-section">
      <h3>5. Paiement, séquestre et commission</h3>
      <ul class="checklist">
        <li>Les paiements sont séquestrés par BERRNI jusqu’à confirmation de la livraison.</li>
        <li>Une commission est prélevée par la plateforme pour couvrir les frais de service, de sécurité et de développement.</li>
        <li>Aucun paiement direct hors plateforme n’est autorisé.</li>
      </ul>
      <p>Ce mécanisme vise à protéger à la fois l’expéditeur et le relais.</p>
    </article>

    <article class="cgu-section">
      <h3>6. Sécurité et lutte contre la fraude</h3>
      <p>BERRNI met en place :</p>
      <ul class="checklist">
        <li>des outils de traçabilité,</li>
        <li>des systèmes de notation,</li>
        <li>des contrôles manuels et automatisés,</li>
        <li>un service SOS Colis en cas d’incident.</li>
      </ul>
      <p>Toute tentative de fraude, contournement ou abus peut entraîner une suspension immédiate du compte.</p>
    </article>

    <article class="cgu-section">
      <h3>7. Limitation de responsabilité</h3>
      <ul class="checklist">
        <li>BERRNI n’intervient pas physiquement dans le transport,</li>
        <li>ne peut être tenue responsable du contenu réel d’un colis non déclaré,</li>
        <li>agit comme facilitateur et tiers de confiance.</li>
      </ul>
      <p>La responsabilité de BERRNI est strictement limitée au périmètre technique et organisationnel de la plateforme.</p>
    </article>

    <article class="cgu-section">
      <h3>8. Suspension et résiliation de compte</h3>
      <ul class="checklist">
        <li>BERRNI se réserve le droit de suspendre temporairement ou définitivement un compte,</li>
        <li>sans préavis, en cas de non-respect des CGU,</li>
        <li>ou pour des raisons de sécurité, légales ou opérationnelles.</li>
      </ul>
    </article>

    <article class="cgu-section">
      <h3>9. Données personnelles</h3>
      <p>L’utilisation de BERRNI implique la collecte et le traitement de données personnelles, conformément à la <a href="{{ url('/confidentialite') }}">Politique de confidentialité</a> en vigueur.</p>
    </article>

    <article class="cgu-section">
      <h3>10. Modification des CGU</h3>
      <p>BERRNI se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés de toute mise à jour majeure.</p>
    </article>

    <article class="cgu-section">
      <h3>11. Droit applicable</h3>
      <p>Les présentes CGU sont régies par le droit en vigueur dans les pays d’exploitation de BERRNI. En cas de litige, une solution amiable sera privilégiée avant toute action judiciaire.</p>
    </article>
  </div>
</section>

    <section class="cta-banner">
      <div class="cta-inner">
        <h3 class="cta-title">Prêt à essayer BERRNI ?</h3>
        <p>Téléchargez l’application dès maintenant et envoyez votre colis en toute confiance !</p>
        <a class="cta-btn" href="#" aria-label="Télécharger l’application">Télécharger l’application</a>
      </div>
    </section>
@endsection