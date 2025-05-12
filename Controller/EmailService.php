<?php
require_once __DIR__ . '/../config/mail.php';

class EmailService {
    private static $instance = null;
    private $mailer;
    
    private function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Configuration du serveur SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->Port = SMTP_PORT;
        $this->mailer->SMTPAuth = SMTP_AUTH;
        $this->mailer->Username = SMTP_USERNAME;
        $this->mailer->Password = SMTP_PASSWORD;
        $this->mailer->SMTPSecure = SMTP_SECURE;
        
        // Configuration de l'expéditeur
        $this->mailer->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
public function send($to, $subject, $body) {
    try {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($to);
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;
        if ($this->mailer->send()) {
            return true;
        } else {
            throw new Exception('Échec de l\'envoi de l\'email.');
        }
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email : " . $e->getMessage());
        return $e->getMessage();  // Retourner le message d'erreur pour un diagnostic plus facile.
    }
}

}
