@extends("layout")

@section("title", "LogiCar — Gestion des plannings et de la flotte")
@section("description", "Préparez les plannings de transport du personnel, suivez les mouvements et gérez votre flotte avec LogiCar.")

@section("content")
    <div class="landing-page">
        <header class="landing-header sticky-top">
            <div class="landing-header__inner">
                <a class="landing-brand" href="{{ route("home") }}" aria-label="LogiCar, accueil">
                    <img src="{{ asset("images/logosof.png") }}" alt="Sofitrans">
                </a>
                <nav class="landing-nav" aria-label="Navigation principale">
                    <a class="landing-nav__link" href="#fonctionnalites">Fonctionnalités</a>
                    <a class="landing-nav__link" href="#espaces">Les espaces</a>
                    <a class="landing-nav__login" href="{{ route("login") }}">
                        Se connecter
                        <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                    </a>
                </nav>
            </div>
        </header>

        <main class="landing-main">
            <section class="landing-hero" aria-labelledby="landing-title">
                <div class="landing-hero__copy">
                    <p class="landing-eyebrow">
                        <span class="landing-eyebrow__dot" aria-hidden="true"></span>
                        La gestion du transport, en toute clarté
                    </p>
                    <h1 id="landing-title">Le transport du personnel, <span>organisé simplement.</span></h1>
                    <p class="landing-hero__description">
                        Préparez les plannings, suivez les trajets et gérez votre flotte depuis un espace adapté
                        à chaque service.
                    </p>
                    <div class="landing-hero__actions">
                        <a class="landing-button landing-button--primary" href="{{ route("login") }}">
                            Accéder à mon espace
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                        <a class="landing-button landing-button--secondary" href="#fonctionnalites">
                            Découvrir LogiCar
                            <i class="bi bi-chevron-down" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="landing-hero__note">
                        <span class="landing-hero__note-icon" aria-hidden="true">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <span>Un accès organisé selon votre service</span>
                    </div>
                </div>

                <div class="landing-visual" aria-hidden="true">
                    <div class="landing-visual__glow" aria-hidden="true"></div>
                    <div class="landing-dashboard">
                        <div class="landing-dashboard__top">
                            <div>
                                <span class="landing-dashboard__kicker">VOTRE ACTIVITÉ</span>
                                <h2>Tout au même endroit</h2>
                            </div>
                            <span class="landing-dashboard__mark" aria-hidden="true">
                                <i class="bi bi-grid-1x2-fill"></i>
                            </span>
                        </div>

                        <div class="landing-dashboard__summary">
                            <div class="landing-summary-card landing-summary-card--blue">
                                <span class="landing-summary-card__icon" aria-hidden="true">
                                    <i class="bi bi-calendar-week"></i>
                                </span>
                                <span class="landing-summary-card__label">Plannings</span>
                                <span class="landing-summary-card__detail">Saisie et consultation</span>
                            </div>
                            <div class="landing-summary-card landing-summary-card--gold">
                                <span class="landing-summary-card__icon" aria-hidden="true">
                                    <i class="bi bi-truck-front"></i>
                                </span>
                                <span class="landing-summary-card__label">Flotte</span>
                                <span class="landing-summary-card__detail">Cars et mouvements</span>
                            </div>
                        </div>

                        <div class="landing-dashboard__activity">
                            <div class="landing-dashboard__activity-heading">
                                <span class="landing-dashboard__activity-icon" aria-hidden="true">
                                    <i class="bi bi-geo-alt"></i>
                                </span>
                                <div>
                                    <strong>Suivi des opérations</strong>
                                    <span>Les informations utiles à vos équipes</span>
                                </div>
                            </div>
                            <div class="landing-dashboard__line">
                                <span class="landing-dashboard__line-dot"></span>
                                <span class="landing-dashboard__line-bar"></span>
                            </div>
                            <div class="landing-dashboard__tags">
                                <span><i class="bi bi-people" aria-hidden="true"></i> Employés</span>
                                <span><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Anomalies</span>
                                <span><i class="bi bi-bar-chart" aria-hidden="true"></i> Statistiques</span>
                            </div>
                        </div>
                    </div>
                    <div class="landing-visual__badge">
                        <span class="landing-visual__badge-icon" aria-hidden="true">
                            <i class="bi bi-check2"></i>
                        </span>
                        <span><strong>Une vue claire</strong><small>sur vos opérations</small></span>
                    </div>
                </div>
            </section>

            <section class="landing-features" id="fonctionnalites" aria-labelledby="features-title">
                <div class="landing-section-heading">
                    <p class="landing-section-heading__eyebrow">Les outils essentiels</p>
                    <h2 id="features-title">Une organisation plus fluide, au quotidien.</h2>
                    <p>Les informations de planification et de flotte réunies dans une seule application.</p>
                </div>

                <div class="landing-feature-grid">
                    <article class="landing-feature-card">
                        <span class="landing-feature-card__icon landing-feature-card__icon--blue" aria-hidden="true">
                            <i class="bi bi-calendar2-check"></i>
                        </span>
                        <h3>Plannings de transport</h3>
                        <p>Préparez et consultez les prises de service, les trajets et les points de ramassage.</p>
                        <span class="landing-feature-card__index">01</span>
                    </article>
                    <article class="landing-feature-card">
                        <span class="landing-feature-card__icon landing-feature-card__icon--gold" aria-hidden="true">
                            <i class="bi bi-truck-front"></i>
                        </span>
                        <h3>Gestion de la flotte</h3>
                        <p>Retrouvez les cars, leur disponibilité, les mouvements et les kilomètres parcourus.</p>
                        <span class="landing-feature-card__index">02</span>
                    </article>
                    <article class="landing-feature-card">
                        <span class="landing-feature-card__icon landing-feature-card__icon--coral" aria-hidden="true">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </span>
                        <h3>Suivi des anomalies</h3>
                        <p>Signalez les anomalies et gardez leur suivi accessible aux personnes concernées.</p>
                        <span class="landing-feature-card__index">03</span>
                    </article>
                </div>
            </section>

            <section class="landing-spaces" id="espaces" aria-labelledby="spaces-title">
                <div class="landing-spaces__intro">
                    <p class="landing-section-heading__eyebrow">Un outil, des besoins différents</p>
                    <h2 id="spaces-title">Votre espace s’adapte à votre service.</h2>
                    <p>Après connexion, LogiCar vous dirige vers les outils correspondant à votre rôle.</p>
                    <a class="landing-text-link" href="{{ route("login") }}">
                        Découvrir mon espace
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="landing-space-list">
                    <article class="landing-space">
                        <span class="landing-space__icon" aria-hidden="true"><i class="bi bi-sliders"></i></span>
                        <div>
                            <h3>Équipe SNR</h3>
                            <p>Suivi des plannings, des cars, des mouvements et des anomalies.</p>
                        </div>
                        <i class="bi bi-arrow-up-right landing-space__arrow" aria-hidden="true"></i>
                    </article>
                    <article class="landing-space">
                        <span class="landing-space__icon landing-space__icon--green" aria-hidden="true">
                            <i class="bi bi-person-lines-fill"></i>
                        </span>
                        <div>
                            <h3>Autres services</h3>
                            <p>Saisie et consultation des plannings de transport des équipes.</p>
                        </div>
                        <i class="bi bi-arrow-up-right landing-space__arrow" aria-hidden="true"></i>
                    </article>
                </div>
            </section>

            <section class="landing-cta" aria-labelledby="cta-title">
                <div>
                    <p class="landing-section-heading__eyebrow">Prêt à continuer ?</p>
                    <h2 id="cta-title">Retrouvez votre espace LogiCar.</h2>
                    <p>Connectez-vous avec votre compte pour accéder aux outils de votre service.</p>
                </div>
                <a class="landing-button landing-button--light" href="{{ route("login") }}">
                    Se connecter
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </section>
        </main>

        <footer class="landing-footer">
            <a class="landing-footer__brand" href="{{ route("home") }}">LogiCar</a>
            <span>Gestion des plannings et du transport du personnel</span>
            <span>&copy; {{ now()->year }} Sofitrans</span>
        </footer>
    </div>
@endsection
