<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConsult - Plateforme de consultation médicale en ligne</title>
    <link rel="stylesheet" href="{{ asset('css/style_welcome.css') }}">
</head>
<body>
    <!-- En-tête -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <img src="{{ asset('images/logo1.png') }}" alt="MediConsult Logo">
                Medi<span>Consult</span>
            </div>
            <nav>
                <ul>
                    <li><a href="#home">Accueil</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#doctors">Médecins</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
            @if (Route::has('login'))
                <div class="auth-buttons">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-secondary">Créer un compte</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </header>

    <!-- Hero section -->
    <section class="hero" id="home">
        <div class="container">
            <h1>La santé à portée de clic</h1>
            <p>Consultez votre médecin en ligne, prenez rendez-vous et accédez à votre dossier médical depuis chez vous.</p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-secondary">Créer un compte</a>
            @endif
        </div>
    </section>

    <!-- Fonctionnalités -->
    <section class="features" id="services">
        <div class="container">
            <h2 class="section-title">Nos services</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><img src="{{ asset('assets/icons/calendar.png') }}" alt="Calendrier"></div>
                    <h3>Prise de rendez-vous</h3>
                    <p>Réservez facilement votre créneau en ligne avec votre médecin. Système de rappel pour ne manquer aucun rendez-vous.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="{{ asset('assets/icons/consultation.png') }}" alt="Consultation"></div>
                    <h3>Consultation en ligne</h3>
                    <p>Consultez votre médecin par vidéo, partagez des documents et communiquez en toute sécurité.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="{{ asset('assets/icons/dossier-medical.png') }}" alt="Dossier"></div>
                    <h3>Dossiers médicaux</h3>
                    <p>Accédez à votre dossier médical complet, vos ordonnances et résultats d'analyses à tout moment.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="{{ asset('assets/icons/prescription.png') }}" alt="Ordonnance"></div>
                    <h3>Ordonnances électroniques</h3>
                    <p>Recevez vos ordonnances signées électroniquement et envoyez-les directement à votre pharmacie.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="{{ asset('assets/icons/messagerie.png') }}" alt="Message"></div>
                    <h3>Messagerie sécurisée</h3>
                    <p>Échangez avec votre médecin via notre système de messagerie crypté et sécurisé.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="{{ asset('assets/icons/tableau-de-bord.png') }}" alt="Tableau de bord"></div>
                    <h3>Tableaux de bord</h3>
                    <p>Suivez vos rendez-vous, consultations et documents médicaux depuis votre espace personnel.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section class="doctors" id="doctors">
        <div class="container">
            <h2 class="section-title">Nos médecins</h2>
            <div class="doctors-grid">
                <div class="doctor-card">
                    <div class="doctor-img"><img src="{{ asset('assets/doctors/Sophie_Martin.png') }}" alt="Dr. Sophie Martin"></div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. Sophie Martin</h3>
                        <div class="doctor-specialty">Médecine générale</div>
                        <p class="doctor-bio">Spécialisée en médecine préventive avec plus de 15 ans d'expérience. Disponible pour des consultations en ligne et en cabinet.</p>
                        <div class="doctor-contact">
                            <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> sophie.martin@mediconsult.fr</p>
                            <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> +33 6 12 34 56 78</p>
                            <p><img src="{{ asset('assets/icons/facebook.png') }}" alt="Facebook" class="contact-icon"> @DrSophieMartin</p>
                            <p><img src="{{ asset('assets/icons/instagram.png') }}" alt="Instagram" class="contact-icon"> @dr.sophie_martin</p>
                        </div>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-img"><img src="{{ asset('assets/doctors/Thomas_Dubois.png') }}" alt="Dr. Thomas Dubois"></div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. Thomas Dubois</h3>
                        <div class="doctor-specialty">Cardiologie</div>
                        <p class="doctor-bio">Cardiologue certifié avec expertise en télémédecine cardiaque. Suivi à distance des patients souffrant de maladies cardiovasculaires.</p>
                        <div class="doctor-contact">
                            <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> thomas.dubois@mediconsult.fr</p>
                            <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> +33 6 23 45 67 89</p>
                            <p><img src="{{ asset('assets/icons/facebook.png') }}" alt="Facebook" class="contact-icon"> @DrThomasDubois</p>
                            <p><img src="{{ asset('assets/icons/instagram.png') }}" alt="Instagram" class="contact-icon"> @dr.thomas_cardio</p>
                        </div>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-img"><img src="{{ asset('assets/doctors/Nadia_Benali.png') }}" alt="Dr. Nadia Benali"></div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. Nadia Benali</h3>
                        <div class="doctor-specialty">Pédiatrie</div>
                        <p class="doctor-bio">Spécialiste en pédiatrie avec une approche bienveillante. Consultation pour les enfants de tous âges et conseils aux parents.</p>
                        <div class="doctor-contact">
                            <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> nadia.benali@mediconsult.fr</p>
                            <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> +33 6 34 56 78 90</p>
                            <p><img src="{{ asset('assets/icons/facebook.png') }}" alt="Facebook" class="contact-icon"> @DrNadiaBenali</p>
                            <p><img src="{{ asset('assets/icons/instagram.png') }}" alt="Instagram" class="contact-icon"> @dr.nadia_pediatre</p>
                        </div>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-img"><img src="{{ asset('assets/doctors/Antoine_Leroux.png') }}" alt="Dr. Antoine Leroux"></div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. Antoine Leroux</h3>
                        <div class="doctor-specialty">Dermatologie</div>
                        <p class="doctor-bio">Expert en dermatologie clinique et esthétique. Consultation à distance possible pour les problèmes cutanés courants.</p>
                        <div class="doctor-contact">
                            <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> antoine.leroux@mediconsult.fr</p>
                            <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> +33 6 45 67 89 01</p>
                            <p><img src="{{ asset('assets/icons/facebook.png') }}" alt="Facebook" class="contact-icon"> @DrAntoineLeroux</p>
                            <p><img src="{{ asset('assets/icons/instagram.png') }}" alt="Instagram" class="contact-icon"> @dr.antoine_derma</p>
                        </div>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-img"><img src="{{ asset('assets/doctors/Julie_Moreau.jpg') }}" alt="Dr. Julie Moreau"></div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. Julie Moreau</h3>
                        <div class="doctor-specialty">Psychiatrie</div>
                        <p class="doctor-bio">Psychiatre spécialisée en thérapies à distance. Accompagnement pour troubles anxieux, dépression et gestion du stress.</p>
                        <div class="doctor-contact">
                            <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> julie.moreau@mediconsult.fr</p>
                            <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> +33 6 56 78 90 12</p>
                            <p><img src="{{ asset('assets/icons/facebook.png') }}" alt="Facebook" class="contact-icon"> @DrJulieMoreau</p>
                            <p><img src="{{ asset('assets/icons/instagram.png') }}" alt="Instagram" class="contact-icon"> @dr.julie_psy</p>
                        </div>
                    </div>
                </div>
                <div class="doctor-card">
                    <div class="doctor-img"><img src="{{ asset('assets/doctors/Michel_Fournier.jpg') }}" alt="Dr. Michel Fournier"></div>
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. Michel Fournier</h3>
                        <div class="doctor-specialty">Endocrinologie</div>
                        <p class="doctor-bio">Spécialiste du diabète et des troubles hormonaux. Suivi personnalisé et conseils nutritionnels pour les patients.</p>
                        <div class="doctor-contact">
                            <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> michel.fournier@mediconsult.fr</p>
                            <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> +33 6 67 89 01 23</p>
                            <p><img src="{{ asset('assets/icons/facebook.png') }}" alt="Facebook" class="contact-icon"> @DrMichelFournier</p>
                            <p><img src="{{ asset('assets/icons/instagram.png') }}" alt="Instagram" class="contact-icon"> @dr.michel_endo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>MediConsult</h3>
                    <p>Votre plateforme de consultation médicale en ligne, sécurisée et simple d'utilisation.</p>
                </div>
                <div class="footer-section">
                    <h3>Liens rapides</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Accueil</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#doctors">Médecins</a></li>
                        <li><a href="login.html">Connexion</a></li>
                        <li><a href="signup.html">Inscription</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <ul class="footer-links" id="contact">
                        <li>Email: mediconsult@gmail.com</li>
                        <li>Téléphone: +212 6 23 45 67 89</li>
                        <li>Adresse: 123 Avenue de la Santé, Al Hoceima</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Légal</h3>
                    <ul class="footer-links">
                        <li><a >Politique de confidentialité</a></li>
                        <li><a >Conditions d'utilisation</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© 2025 MediConsult. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
