<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConsult - Plateforme de consultation médicale en ligne</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Laravel asset injection --}}
    <style>
        /* All your CSS styles from the original code here */
        /* Variables et styles de base */
        :root {
            --primary: #1976d2;
            --primary-light: #4791db;
            --primary-dark: #115293;
            --secondary: #43a047;
            --secondary-light: #76d275;
            --secondary-dark: #2d7031;
            --danger: #e53935;
            --warning: #ffb74d;
            --text-dark: #333;
            --text-light: #f5f5f5;
            --bg-light: #f8f9fa;
            --bg-white: #ffffff;
            --shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        a {
            text-decoration: none;
            color: var(--primary);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* En-tête */
        header {
            background-color: var(--bg-white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary);
        }

        .logo span {
            color: var(--secondary);
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            background-color: var(--primary-light);
            color: var(--text-light);
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--text-light);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: var(--text-light);
        }

        .btn-secondary:hover {
            background-color: var(--secondary-dark);
        }

        /* Hero section */
        .hero {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            color: var(--text-light);
            padding: 80px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }

        /* Fonctionnalités */
        .features {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 2.2rem;
            color: var(--primary-dark);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background-color: var(--bg-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 30px;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .feature-card h3 {
            margin-bottom: 15px;
            color: var(--primary-dark);
        }

        /* Authentification Section Styling */
        .authentication {
            padding: 80px 0;
            /* Added padding for consistency */
        }

        .auth-access-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .auth-access-buttons .btn {
            margin: 0 10px;
            /* Add some space between buttons */
        }


        /* REMOVED styles for .auth-forms, .login-form, .register-form, .form-title, .form-group, etc. as they are no longer needed here */

        /* ... (Keep other styles like Calendar, Dashboard, etc. if they exist in your full file) ... */

        /* Footer */
        footer {
            background-color: var(--primary-dark);
            color: var(--text-light);
            padding: 50px 0 20px;
            margin-top: 80px;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 10px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: var(--text-light);
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .footer-links a:hover {
            opacity: 1;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo span {
            color: #00bcd4;
        }


        /* Media queries pour responsivité */
        @media (max-width: 768px) {
            /* REMOVED styles for .auth-forms direction change */

            .dashboard,
            .consultation {
                /* Assuming these exist */
                flex-direction: column;
            }

            .sidebar {
                /* Assuming this exists */
                width: 100%;
                margin-bottom: 30px;
            }

            .calendar-grid {
                /* Assuming this exists */
                grid-template-columns: repeat(1, 1fr);
            }

            .header-container {
                flex-direction: column;
                padding: 15px;
                /* Adjust padding */
            }

            nav ul {
                flex-wrap: wrap;
                /* Allow nav items to wrap */
                justify-content: center;
                /* Center nav items */
                margin-top: 15px;
                /* Adjust margin */
                margin-left: 0;
                /* Remove left margin */
            }

            nav ul li {
                margin: 5px 10px;
                /* Adjust spacing for wrapped items */
            }

            .auth-buttons {
                margin-top: 15px;
                /* Add space above buttons on mobile */
            }
        }
    </style>
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
            <p>Consultez votre médecin en ligne, prenez rendez-vous et accédez à votre dossier médical depuis chez vous.
            </p>
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
                    <div class="feature-icon">📅</div>
                    <h3>Prise de rendez-vous</h3>
                    <p>Réservez facilement votre créneau en ligne avec votre médecin.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🗣</div>
                    <h3>Consultation en ligne</h3>
                    <p>Consultez votre médecin par vidéo en toute sécurité.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📁</div>
                    <h3>Dossiers médicaux</h3>
                    <p>Accédez à votre dossier médical complet à tout moment.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💊</div>
                    <h3>Ordonnances électroniques</h3>
                    <p>Recevez vos ordonnances et envoyez-les à votre pharmacie.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💬</div>
                    <h3>Messagerie sécurisée</h3>
                    <p>Échangez avec votre médecin via messagerie cryptée.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Tableaux de bord</h3>
                    <p>Suivez vos activités médicales en un clin d'œil.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="footer-container">
                <div class="footer-section">
                    <h3>À propos</h3>
                    <ul class="footer-links">
                        <li><a href="#">Qui sommes-nous</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="#">Aide</a></li>
                        <li><a href="#">Contactez-nous</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                © {{ date('Y') }} MediConsult. Tous droits réservés.
            </div>
        </div>
    </footer>
</body>

</html>
