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
            @guest {{-- Show only if user is not logged in --}}
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-secondary">Créer un compte</a>
                @endif
            @endguest
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
                {{-- Check if there are any doctors to display --}}
                @if(isset($doctors_list) && $doctors_list->count() > 0)
                    @foreach($doctors_list as $doctor_user)
                        <div class="doctor-card">
                            <div class="doctor-img">
                                @if($doctor_user->photo_path)
                                    <img src="{{ asset('storage/' . $doctor_user->photo_path) }}" alt="Dr. {{ $doctor_user->name }}">
                                @else
                                    {{-- Make sure you have a default_doctor.png in public/assets/doctors/ --}}
                                    <img src="{{ asset('assets/doctors/default_doctor.png') }}" alt="Dr. {{ $doctor_user->name }}">
                                @endif
                            </div>
                            <div class="doctor-info">
                                <h3 class="doctor-name">Dr. {{ $doctor_user->name }}</h3>
                                <div class="doctor-specialty">
                                    {{-- Access specialty from the related doctor model --}}
                                    {{ $doctor_user->doctor->specialty ?? 'Spécialité non définie' }}
                                </div>
                                <p class="doctor-bio">
                                    {{-- Access bio from the related doctor model --}}
                                    {{ $doctor_user->doctor->bio ?? 'Aucune biographie disponible.' }}
                                </p>
                                <div class="doctor-contact">
                                    <p><img src="{{ asset('assets/icons/email.png') }}" alt="Email" class="contact-icon"> {{ $doctor_user->email }}</p>
                                    @if($doctor_user->phone_number)
                                        <p><img src="{{ asset('assets/icons/phone.png') }}" alt="Téléphone" class="contact-icon"> {{ $doctor_user->phone_number }}</p>
                                    @endif
                                    {{-- Check if practice_address exists and is not the default "Vide" --}}
                                    @if(isset($doctor_user->doctor->practice_address) && $doctor_user->doctor->practice_address !== 'Vide' && $doctor_user->doctor->practice_address !== null)
                                        <p><img src="{{ asset('assets/icons/location.png') }}" alt="Adresse" class="contact-icon"> {{ $doctor_user->doctor->practice_address }}</p>
                                        {{-- Ensure you have location.png in public/assets/icons/ --}}
                                    @endif
                                    {{-- Add social media links here if you store them in the database --}}
                                </div>
                                {{-- Button to take action, e.g., view profile or book appointment --}}
                                {{-- For now, it links to login, assuming booking requires login --}}
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm" style="margin-top: 15px;">Prendre RDV</a>
                                {{--
                                    Later, you might have a public profile page for each doctor:
                                    <a href="{{ route('doctor.public.profile', $doctor_user->id) }}" class="btn btn-primary btn-sm" style="margin-top: 15px;">Voir Profil</a>
                                --}}
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; grid-column: 1 / -1; padding: 20px;">Aucun médecin n'est actuellement disponible sur notre plateforme.</p>
                @endif
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
                        @guest
                            <li><a href="{{ route('login') }}">Connexion</a></li>
                            <li><a href="{{ route('register') }}">Inscription</a></li>
                        @else
                            <li><a href="{{ route('dashboard') }}">Mon Espace</a></li>
                        @endguest
                    </ul>
                </div>
                <div class="footer-section" id="contact"> {{-- Added id here as per your nav link --}}
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li>Email: mediconsult@example.com</li> {{-- Use a placeholder or real email --}}
                        <li>Téléphone: +212 6 00 00 00 00</li> {{-- Use a placeholder or real phone --}}
                        <li>Adresse: 123 Avenue de la Santé, Ville, Maroc</li> {{-- Use a placeholder or real address --}}
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Légal</h3>
                    <ul class="footer-links">
                        <li><a href="#">Politique de confidentialité</a></li> {{-- Link to actual pages later --}}
                        <li><a href="#">Conditions d'utilisation</a></li> {{-- Link to actual pages later --}}
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© {{ date('Y') }} MediConsult. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
