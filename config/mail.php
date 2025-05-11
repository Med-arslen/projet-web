<?php
// =========================
// CONFIGURATION SMTP UNIVERSELLE POUR PHPMailer
// =========================
// Décommente la section correspondant au fournisseur que tu veux utiliser
// et commente les autres.

// --- 1. Mercury local (pour tests locaux uniquement) ---
// define('SMTP_HOST', 'localhost');
// define('SMTP_PORT', 25);
// define('SMTP_AUTH', false);
// define('SMTP_USERNAME', '');
// define('SMTP_PASSWORD', '');
// define('SMTP_SECURE', '');
// define('SMTP_FROM', 'admin@movievibe.com');
// define('SMTP_FROM_NAME', 'MovieVibe Support');

// --- 2. Gmail ---
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'emnabenaissa554@gmail.com');
// Mot de passe d'application généré : tddhnoomulglldpj
define('SMTP_PASSWORD', 'tddhnoomulglldpj');
define('SMTP_SECURE', 'tls');
define('SMTP_FROM', 'emnabenaissa554@gmail.com');
define('SMTP_FROM_NAME', 'MovieVibe Support');

// --- 3. Outlook/Hotmail ---
// define('SMTP_HOST', 'smtp.office365.com');
// define('SMTP_PORT', 587);
// define('SMTP_AUTH', true);
// define('SMTP_USERNAME', 'ton_adresse@outlook.com');
// define('SMTP_PASSWORD', 'ton_mot_de_passe');
// define('SMTP_SECURE', 'tls');
// define('SMTP_FROM', 'ton_adresse@outlook.com');
// define('SMTP_FROM_NAME', 'MovieVibe Support');

// --- 4. Yahoo ---
// define('SMTP_HOST', 'smtp.mail.yahoo.com');
// define('SMTP_PORT', 587);
// define('SMTP_AUTH', true);
// define('SMTP_USERNAME', 'ton_adresse@yahoo.com');
// define('SMTP_PASSWORD', 'ton_mot_de_passe_application');
// define('SMTP_SECURE', 'tls');
// define('SMTP_FROM', 'ton_adresse@yahoo.com');
// define('SMTP_FROM_NAME', 'MovieVibe Support');

// --- 5. Serveur SMTP professionnel (OVH, Gandi, etc.) ---
// define('SMTP_HOST', 'ssl0.ovh.net'); // ou le host de ton fournisseur
// define('SMTP_PORT', 587);
// define('SMTP_AUTH', true);
// define('SMTP_USERNAME', 'ton_email@tondomaine.com');
// define('SMTP_PASSWORD', 'ton_mot_de_passe');
// define('SMTP_SECURE', 'tls');
// define('SMTP_FROM', 'ton_email@tondomaine.com');
// define('SMTP_FROM_NAME', 'MovieVibe Support');

// Configuration des chemins
define('MERCURY_PATH', 'C:/xampp/MercuryMail');
define('MERCURY_CONFIG', 'C:/xampp/MercuryMail/MERCURY32.INI');
define('MERCURY_EXE', 'C:/xampp/MercuryMail/mercury.exe');
define('SENDMAIL_PATH', 'C:/xampp/sendmail/sendmail.exe'); 

$mail->addAddress('emnabenaissa62@gmail.com'); 