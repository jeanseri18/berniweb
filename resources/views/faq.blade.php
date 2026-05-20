@extends('layouts.app')

@section('content')
<section class="faq-hero">
  <div class="container">
    <h1 class="faq-title">Foire Aux Questions</h1>
    <p class="faq-lead">Trouvez des réponses claires à vos questions sur BERRNI.</p>
    <div class="faq-search">
      <i class="bi bi-search"></i>
      <input type="text" placeholder="Rechercher" aria-label="Rechercher dans la FAQ" />
    </div>
  </div>
</section>

<section class="faq-body">
  <div class="faq-container">

    <details class="faq-category" open>
      <summary>
        <span>Application BERRNI</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item" open>
          <summary>Qu’est-ce que l’application BERRNI ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer">
            <p>BERRNI est une application mobile qui permet d’envoyer des colis entre villes en les confiant à des particuliers de confiance qui effectuent déjà le trajet. L’application sécurise les échanges et accompagne chaque étape.</p>
          </div>
        </details>
        <details class="faq-item">
          <summary>Où puis-je télécharger l’application BERRNI ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer">
            <p>L’application BERRNI est disponible sur les stores officiels (Google Play et App Store). Le site web BERRNI permet uniquement de s’informer et redirige vers l’application.</p>
          </div>
        </details>
        <details class="faq-item">
          <summary>Puis-je utiliser BERRNI sans créer de compte ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer">
            <p>Vous pouvez consulter le site sans compte, mais toute action liée à un colis (envoyer, transporter, suivre, payer) nécessite un compte sur l’application.</p>
          </div>
        </details>
        <details class="faq-item">
          <summary>Comment créer un compte sur BERRNI ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer">
            <p>La création de compte se fait simplement avec un numéro de téléphone et un code de vérification (OTP). Aucune information inutile n’est demandée.</p>
          </div>
        </details>
        <details class="faq-item">
          <summary>Puis-je avoir plusieurs comptes ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer">
            <p>Non. Un utilisateur dispose d’un seul compte, dans lequel il peut activer le rôle d’expéditeur et/ou de relais de confiance.</p>
          </div>
        </details>
        <details class="faq-item">
          <summary>BERRNI est-elle disponible en Côte d’Ivoire et au Cameroun ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer">
            <p>Oui. BERRNI est conçue pour fonctionner en Côte d’Ivoire et au Cameroun, avec une extension progressive vers d’autres pays.</p>
          </div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>Expéditeur</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Qui peut envoyer un colis sur BERRNI ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Toute personne disposant de l’application et d’un compte actif peut envoyer un colis.</p></div>
        </details>
        <details class="faq-item">
          <summary>Quels types de colis puis-je envoyer ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Vous pouvez envoyer des colis légers, personnels ou commerciaux, dans le respect des règles de sécurité et des objets autorisés.</p></div>
        </details>
        <details class="faq-item">
          <summary>Comment fixer le prix de l’envoi ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>C’est vous qui fixez librement la rémunération proposée au relais. BERRNI peut afficher des fourchettes indicatives, mais le choix final vous appartient.</p></div>
        </details>
        <details class="faq-item">
          <summary>Quand dois-je payer ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Le paiement est effectué au moment de la publication du colis, mais il est bloqué (séquestré) jusqu’à la livraison.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je discuter avec le relais avant d’accepter ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui. Une négociation encadrée est prévue (3 échanges maximum) pour garantir des discussions claires et respectueuses.</p></div>
        </details>
        <details class="faq-item">
          <summary>Que se passe-t-il après la livraison ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Vous confirmez la réception du colis. Le paiement est alors automatiquement libéré au relais.</p></div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>Relais de confiance</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Qui peut devenir relais de confiance ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Toute personne majeure effectuant un trajet intercity (bus, train, voiture personnelle, avion) peut devenir relais.</p></div>
        </details>
        <details class="faq-item">
          <summary>Comment activer le rôle de relais ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Depuis l’application, vous activez simplement le rôle “Relais de confiance” et déclarez vos trajets.</p></div>
        </details>
        <details class="faq-item">
          <summary>Suis-je obligé d’accepter tous les colis ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Non. Vous êtes libre d’accepter ou de refuser toute proposition.</p></div>
        </details>
        <details class="faq-item">
          <summary>Quand suis-je payé ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Vous êtes payé uniquement après la confirmation de la livraison par l’expéditeur.</p></div>
        </details>
        <details class="faq-item">
          <summary>Le paiement est-il garanti ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui. Le paiement est sécurisé par BERRNI et bloqué dès la publication du colis.</p></div>
        </details>
        <details class="faq-item">
          <summary>Que se passe-t-il en cas de problème ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Le service SOS Colis permet une médiation humaine avec l’équipe BERRNI.</p></div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>Paiement & Séquestre</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Qu’est-ce que le paiement sous séquestre ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Le montant de la livraison est bloqué par BERRNI jusqu’à la confirmation de la remise du colis.</p></div>
        </details>
        <details class="faq-item">
          <summary>Pourquoi ce système est-il utilisé ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Il protège à la fois l’expéditeur et le relais, en garantissant une relation équitable.</p></div>
        </details>
        <details class="faq-item">
          <summary>Quand le paiement est-il libéré ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Après confirmation de la livraison dans l’application.</p></div>
        </details>
        <details class="faq-item">
          <summary>BERRNI prélève-t-elle une commission ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui. Une commission est prélevée automatiquement pour couvrir la sécurité, la plateforme et l’assistance.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je voir l’historique de mes paiements ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui. Un portefeuille BERRNI permet de suivre les montants disponibles et bloqués.</p></div>
        </details>
        <details class="faq-item">
          <summary>Quels moyens de paiement sont acceptés ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Les moyens de paiement locaux (Mobile Money) sont privilégiés.</p></div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>Messagerie & Négociation</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Puis-je discuter librement avec l’autre partie ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>La messagerie est limitée pendant la négociation, puis totalement ouverte après validation.</p></div>
        </details>
        <details class="faq-item">
          <summary>Pourquoi la messagerie est-elle encadrée ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Pour éviter les abus, garantir la clarté et protéger les utilisateurs.</p></div>
        </details>
        <details class="faq-item">
          <summary>Combien d’échanges sont autorisés ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>La négociation est limitée à 3 allers-retours.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je envoyer des coordonnées personnelles ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Non. Les échanges doivent rester dans l’application pour des raisons de sécurité.</p></div>
        </details>
        <details class="faq-item">
          <summary>Les messages sont-ils conservés ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, pour assurer la traçabilité et la médiation en cas de litige.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je bloquer un utilisateur ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, via les paramètres et le support.</p></div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>Suivi du colis</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Comment suivre mon colis ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>L’application affiche des statuts clairs à chaque étape.</p></div>
        </details>
        <details class="faq-item">
          <summary>Le suivi est-il en temps réel ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Le suivi est basé sur des étapes validées par les utilisateurs, pas sur un GPS intrusif.</p></div>
        </details>
        <details class="faq-item">
          <summary>Qui voit le suivi ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>L’expéditeur et le relais.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je recevoir des notifications ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, des notifications intelligentes vous informent à chaque étape clé.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je contacter le relais pendant le trajet ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, après validation de la négociation.</p></div>
        </details>
        <details class="faq-item">
          <summary>Que faire si le colis semble bloqué ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Utilisez la fonction SOS Colis.</p></div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>SOS Colis & Assistance</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Qu’est-ce que SOS Colis ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Un service d’assistance dédié aux problèmes liés à un colis en cours.</p></div>
        </details>
        <details class="faq-item">
          <summary>Quand utiliser SOS Colis ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>En cas de retard, désaccord, ou incident.</p></div>
        </details>
        <details class="faq-item">
          <summary>Comment y accéder ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Uniquement depuis l’application.</p></div>
        </details>
        <details class="faq-item">
          <summary>Que fait BERRNI en cas de litige ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>BERRNI agit comme tiers de confiance et facilite la médiation.</p></div>
        </details>
        <details class="faq-item">
          <summary>Le support WhatsApp remplace-t-il SOS Colis ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Non. WhatsApp est réservé aux questions générales.</p></div>
        </details>
        <details class="faq-item">
          <summary>Mon problème sera-t-il traité rapidement ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui. Les demandes SOS sont prioritaires.</p></div>
        </details>
      </div>
    </details>

    <details class="faq-category">
      <summary>
        <span>Sécurité & Confiance</span>
        <i class="bi bi-chevron-down"></i>
      </summary>
      <div class="faq-items">
        <details class="faq-item">
          <summary>Comment BERRNI sécurise-t-elle les échanges ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Par l’identification des utilisateurs, le séquestre, la traçabilité et l’assistance.</p></div>
        </details>
        <details class="faq-item">
          <summary>Existe-t-il un système de réputation ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, basé sur l’expérience réelle : Initié – Confirmé – Fiable.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je signaler un comportement inapproprié ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, via l’application ou SOS Colis.</p></div>
        </details>
        <details class="faq-item">
          <summary>Mes données sont-elles protégées ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui. BERRNI respecte strictement la confidentialité des données.</p></div>
        </details>
        <details class="faq-item">
          <summary>Puis-je supprimer mon compte ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Oui, à tout moment depuis l’application.</p></div>
        </details>
        <details class="faq-item">
          <summary>BERRNI est-elle une agence de transport ? <i class="bi bi-caret-down-fill"></i></summary>
          <div class="faq-answer"><p>Non. BERRNI est une plateforme de mise en relation sécurisée entre particuliers.</p></div>
        </details>
      </div>
    </details>

    <div class="faq-rules">
      <h2 class="section-title">Règles, chartes & sécurité</h2>
      <div class="rule-card-grid">
        <a href="{{ url('/cgu') }}" class="rule-card">
          <div class="rule-icon"><img src="{{ asset('faq/2008.i301.001_personal_data_protection_gdpr_isometric_icons-04.jpg') }} " style="width: 70px;height: 70px;" alt="CGU BERRNI"></div>
          <div class="rule-content">
            <h3>CGU</h3>
            <p>Conditions générales d’utilisation de BERRNI.</p>
            <span class="rule-action">Consulter <i class="bi bi-arrow-right-short"></i></span>
          </div>
        </a>
        <a href="{{ url('/confidentialite') }}" class="rule-card">
          <div class="rule-icon"><i class="bi bi-shield-lock" style="font-size: 2.5rem; color: #2d6a4f;"></i></div>
          <div class="rule-content">
            <h3>Confidentialité</h3>
            <p>Politique de protection des données personnelles.</p>
            <span class="rule-action">Consulter <i class="bi bi-arrow-right-short"></i></span>
          </div>
        </a>
        <a href="{{ url('/charte-expediteur') }}" class="rule-card">
          <div class="rule-icon"><img src="{{ asset('faq/green-pencil-with-positive-questionnaire.jpg') }}" alt="Charte expéditeur" style="width: 70px;height: 70px;"></div>
          <div class="rule-content">
            <h3>Charte Expéditeur</h3>
            <p>Engagements et responsabilités de l’expéditeur.</p>
            <span class="rule-action">Consulter <i class="bi bi-arrow-right-short"></i></span>
          </div>
        </a>
        <a href="{{ url('/charte') }}" class="rule-card">
          <div class="rule-icon"><img src="{{ asset('faq/Sandy_Bus-36_Single-01.jpg') }}" alt="Charte relais de confiance" style="width: 70px;height: 70px;"></div>
          <div class="rule-content">
            <h3>Charte Relais de confiance</h3>
            <p>Règles et engagements des relais de confiance.</p>
            <span class="rule-action">Consulter <i class="bi bi-arrow-right-short"></i></span>
          </div>
        </a>
      </div>
    </div>



  </div>
</section>
    <section class="cta-banner">
      <div class="cta-inner">
        <h3 class="cta-title">Prêt à essayer BERRNI ?</h3>
        <p>Téléchargez l’application dès maintenant et envoyez votre colis en toute confiance !</p>
        <a class="cta-btn" href="#" aria-label="Télécharger l’application">Télécharger l’application</a>
      </div>
    </section>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.querySelector('.faq-search input');
  if (!input) return;
  const categories = Array.from(document.querySelectorAll('.faq-category'));
  const itemsByCategory = categories.map(cat => ({
    cat,
    items: Array.from(cat.querySelectorAll('.faq-item'))
  }));
  const container = document.querySelector('.faq-container');
  let noEl = document.getElementById('faq-no-results');
  if (!noEl && container) {
    noEl = document.createElement('p');
    noEl.id = 'faq-no-results';
    noEl.textContent = 'Aucun résultat';
    noEl.style.display = 'none';
    noEl.style.textAlign = 'center';
    noEl.style.margin = '16px 0';
    container.appendChild(noEl);
  }
  const filter = (q) => {
    const query = q.trim().toLowerCase();
    let any = false;
    itemsByCategory.forEach(({cat, items}) => {
      let catHas = false;
      items.forEach(item => {
        const text = item.textContent.toLowerCase();
        const match = query === '' || text.includes(query);
        item.style.display = match ? '' : 'none';
        if (match) { catHas = true; any = true; }
      });
      cat.style.display = catHas ? '' : 'none';
      if (catHas && query) { cat.open = true; }
    });
    if (noEl) noEl.style.display = any ? 'none' : '';
  };
  input.addEventListener('input', (e) => filter(e.target.value));
});
</script>
@endsection
