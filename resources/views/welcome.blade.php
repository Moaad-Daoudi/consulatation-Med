<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConsult - Plateforme de consultation médicale en ligne<</title>
    <link rel="stylesheet" href="{{ asset('css/welcome_style.css') }}">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <img src="{{ asset('logo.png') }}" alt="not found">
                Medi <span>Consult</span>
            </div>
            <nav>
                <ul>
                    <li><a href="#home">Accueil</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#">Médecins</a></li>
                    <li><a href="#footer">Contact</a></li>
                </ul>
            </nav>
            <div class="auto-button">
                @guest
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="login-button">Connexion</a>
                    @endif
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="signup-button">Créer un compte</a>
                    @endif
                @else
                    @php
                        $dashboardRoute =
                            match (Auth::user()->role->role) {
                                'admin' => route('admin.dashboard'),
                                'patient' => route('patient.dashboard'),
                                'doctor' => route('doctor.dashboard')
                            }
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="dashboard-button">Dashboard</a>
                @endguest
            </div>
        </div>
    </header>
    <main>
        <section class="hero" id="home">
            <h1>La santé à portée de clic</h1>
            <p>Consultez votre médecin en ligne, prenez rendez-vous et accédez à votre dossier médical depuis chez vous.</p>
            @guest
                @if(Route::has('login'))
                    <a href="{{ route('register') }}" class="signup-button">Créer un compte</a>
                @endif
            @else
                <a href="{{ $dashboardRoute }}" class="dashboard-button">Dashboard</a>
            @endguest
        </section>
        <section class="features" id="services">
            <div class="features-container">
                <h2 class="feature-title">Nos services</h2>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon"><img src="{{ asset('icons/calendar.png') }}" alt="Calendrier"></div>
                        <h3>Prise de rendez-vous</h3>
                        <p>Réservez facilement votre créneau en ligne avec votre médecin. Système de rappel pour ne manquer aucun rendez-vous.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><img src="{{ asset('icons/consultation.png') }}" alt="Consultation"></div>
                        <h3>Consultation en ligne</h3>
                        <p>Consultez votre médecin par vidéo, partagez des documents et communiquez en toute sécurité.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><img src="{{ asset('icons/dossier-medical.png') }}" alt="Dossier"></div>
                        <h3>Dossiers médicaux</h3>
                        <p>Accédez à votre dossier médical complet, vos ordonnances et résultats d'analyses à tout moment.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><img src="{{ asset('icons/prescription.png') }}" alt="Ordonnance"></div>
                        <h3>Ordonnances électroniques</h3>
                        <p>Recevez vos ordonnances signées électroniquement et envoyez-les directement à votre pharmacie.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><img src="{{ asset('icons/messagerie.png') }}" alt="Message"></div>
                        <h3>Messagerie sécurisée</h3>
                        <p>Échangez avec votre médecin via notre système de messagerie crypté et sécurisé.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon"><img src="{{ asset('icons/tableau-de-bord.png') }}" alt="Tableau de bord"></div>
                        <h3>Tableaux de bord</h3>
                        <p>Suivez vos rendez-vous, consultations et documents médicaux depuis votre espace personnel.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <section class="footer-section" id="footer">
            <div class="container">
                <div class="footer-container">
                    <h3>MediConsult</h3>
                    <p>Votre plateforme de consultation médicale en ligne, sécurisée et simple d'utilisation.</p>
                </div>
                <div class="footer-container">
                    <h3>Liens rapides</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Accueil</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#">Médecins</a></li>
                        <li><a href="#footer">Contact</a></li>
                        @guest
                            <li><a href="{{ route('login') }}">Connexion</a></li>
                            <li><a href="{{ route('register') }}">Inscription</a></li>
                        @else
                            <li><a href="{{ $dashboardRoute }}">Mon Espace</a></li>
                        @endguest
                    </ul>
                </div>
                <div class="footer-container">
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li>Email: mediconsult@example.com</li>
                        <li>Téléphone: +212 6 00 00 00 00</li>
                        <li>Adresse: 123 Avenue de la Santé, Ville, Maroc</li>
                    </ul>
                </div>
                <div class="footer-container">
                    <h3>Légal</h3>
                    <ul class="footer-links">
                        <li><a href="#">Politique de confidentialité</a></li>
                        <li><a href="#">Conditions d'utilisation</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© {{ date('Y') }} MediConsult. Tous droits réservés.</p>
            </div>
        </section>
    </footer>
</body>
</html>
