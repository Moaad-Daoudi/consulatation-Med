<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConsult - Plateforme de consultation médicale en ligne</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style_welcome.css')); ?>">
</head>
<body>
    <!-- En-tête -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="MediConsult Logo">
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
            <?php if(Route::has('login')): ?>
                <div class="auth-buttons">
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/dashboard')); ?>" class="btn btn-primary">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-primary">Connexion</a>
                        <?php if(Route::has('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="btn btn-secondary">Créer un compte</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero section -->
    <section class="hero" id="home">
        <div class="container">
            <h1>La santé à portée de clic</h1>
            <p>Consultez votre médecin en ligne, prenez rendez-vous et accédez à votre dossier médical depuis chez vous.</p>
            <?php if(auth()->guard()->guest()): ?> 
                <?php if(Route::has('register')): ?>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-secondary">Créer un compte</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Fonctionnalités -->
    <section class="features" id="services">
        <div class="container">
            <h2 class="section-title">Nos services</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><img src="<?php echo e(asset('assets/icons/calendar.png')); ?>" alt="Calendrier"></div>
                    <h3>Prise de rendez-vous</h3>
                    <p>Réservez facilement votre créneau en ligne avec votre médecin. Système de rappel pour ne manquer aucun rendez-vous.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="<?php echo e(asset('assets/icons/consultation.png')); ?>" alt="Consultation"></div>
                    <h3>Consultation en ligne</h3>
                    <p>Consultez votre médecin par vidéo, partagez des documents et communiquez en toute sécurité.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="<?php echo e(asset('assets/icons/dossier-medical.png')); ?>" alt="Dossier"></div>
                    <h3>Dossiers médicaux</h3>
                    <p>Accédez à votre dossier médical complet, vos ordonnances et résultats d'analyses à tout moment.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="<?php echo e(asset('assets/icons/prescription.png')); ?>" alt="Ordonnance"></div>
                    <h3>Ordonnances électroniques</h3>
                    <p>Recevez vos ordonnances signées électroniquement et envoyez-les directement à votre pharmacie.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="<?php echo e(asset('assets/icons/messagerie.png')); ?>" alt="Message"></div>
                    <h3>Messagerie sécurisée</h3>
                    <p>Échangez avec votre médecin via notre système de messagerie crypté et sécurisé.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><img src="<?php echo e(asset('assets/icons/tableau-de-bord.png')); ?>" alt="Tableau de bord"></div>
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
                
                <?php if(isset($doctors_list) && $doctors_list->count() > 0): ?>
                    <?php $__currentLoopData = $doctors_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor_user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="doctor-card">
                            <div class="doctor-img">
                                <?php if($doctor_user->photo_path): ?>
                                    <img src="<?php echo e(asset('storage/' . $doctor_user->photo_path)); ?>" alt="Dr. <?php echo e($doctor_user->name); ?>">
                                <?php else: ?>
                                    
                                    <img src="<?php echo e(asset('assets/doctors/default_doctor.png')); ?>" alt="Dr. <?php echo e($doctor_user->name); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="doctor-info">
                                <h3 class="doctor-name">Dr. <?php echo e($doctor_user->name); ?></h3>
                                <div class="doctor-specialty">
                                    
                                    <?php echo e($doctor_user->doctor->specialty ?? 'Spécialité non définie'); ?>

                                </div>
                                <p class="doctor-bio">
                                    
                                    <?php echo e($doctor_user->doctor->bio ?? 'Aucune biographie disponible.'); ?>

                                </p>
                                <div class="doctor-contact">
                                    <p><img src="<?php echo e(asset('assets/icons/email.png')); ?>" alt="Email" class="contact-icon"> <?php echo e($doctor_user->email); ?></p>
                                    <?php if($doctor_user->phone_number): ?>
                                        <p><img src="<?php echo e(asset('assets/icons/phone.png')); ?>" alt="Téléphone" class="contact-icon"> <?php echo e($doctor_user->phone_number); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if(isset($doctor_user->doctor->practice_address) && $doctor_user->doctor->practice_address !== 'Vide' && $doctor_user->doctor->practice_address !== null): ?>
                                        <p><img src="<?php echo e(asset('assets/icons/location.png')); ?>" alt="Adresse" class="contact-icon"> <?php echo e($doctor_user->doctor->practice_address); ?></p>
                                        
                                    <?php endif; ?>
                                    
                                </div>
                                
                                
                                <a href="<?php echo e(route('login')); ?>" class="btn btn-primary btn-sm" style="margin-top: 15px;">Prendre RDV</a>
                                
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p style="text-align: center; grid-column: 1 / -1; padding: 20px;">Aucun médecin n'est actuellement disponible sur notre plateforme.</p>
                <?php endif; ?>
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
                        <?php if(auth()->guard()->guest()): ?>
                            <li><a href="<?php echo e(route('login')); ?>">Connexion</a></li>
                            <li><a href="<?php echo e(route('register')); ?>">Inscription</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo e(route('dashboard')); ?>">Mon Espace</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section" id="contact"> 
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li>Email: mediconsult@example.com</li> 
                        <li>Téléphone: +212 6 00 00 00 00</li> 
                        <li>Adresse: 123 Avenue de la Santé, Ville, Maroc</li> 
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Légal</h3>
                    <ul class="footer-links">
                        <li><a href="#">Politique de confidentialité</a></li> 
                        <li><a href="#">Conditions d'utilisation</a></li> 
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© <?php echo e(date('Y')); ?> MediConsult. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/welcome.blade.php ENDPATH**/ ?>