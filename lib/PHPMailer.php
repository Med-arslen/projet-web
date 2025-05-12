<?php
/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 */

class PHPMailer {
    public $SMTPDebug = 0;
    public $CharSet = 'UTF-8';
    public $Host = 'localhost';  // Serveur SMTP local
    public $Port = 25;           // Port SMTP standard
    public $SMTPSecure = '';     // Pas de chiffrement pour le serveur local
    public $SMTPAuth = false;    // Pas d'authentification pour le serveur local
    public $Username = '';       // Pas besoin d'utilisateur
    public $Password = '';       // Pas besoin de mot de passe
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    private $to = array();
    public $ErrorInfo = '';
    
    public function send() {
        if (empty($this->to)) {
            $this->ErrorInfo = 'Aucun destinataire spécifié';
            return false;
        }

        try {
            // Création de la connexion SMTP
            $smtp = fsockopen($this->Host, $this->Port, $errno, $errstr, 30);
            if (!$smtp) {
                throw new Exception("Connexion impossible au serveur SMTP ($errno : $errstr)");
            }

            // Lecture de la réponse initiale
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '220') {
                throw new Exception("Erreur de connexion SMTP: $response");
            }

            // EHLO
            fputs($smtp, "EHLO " . $_SERVER['SERVER_NAME'] . "\r\n");
            // Lire toutes les lignes de réponse EHLO (250-... puis 250 ...)
            do {
                $response = fgets($smtp, 515);
            } while ($response !== false && substr($response, 0, 4) === '250-');
            if ($response === false || substr($response, 0, 3) !== '250') {
                throw new Exception("Erreur EHLO: $response");
            }

            // MAIL FROM
            fputs($smtp, "MAIL FROM: <" . $this->From . ">\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '250') {
                throw new Exception("Erreur MAIL FROM: $response");
            }

            // RCPT TO
            foreach ($this->to as $recipient) {
                fputs($smtp, "RCPT TO: <" . $recipient[0] . ">\r\n");
                $response = fgets($smtp, 515);
                if (substr($response, 0, 3) !== '250') {
                    throw new Exception("Erreur RCPT TO: $response");
                }
            }

            // DATA
            fputs($smtp, "DATA\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '354') {
                throw new Exception("Erreur DATA: $response");
            }

            // Headers
            $headers = $this->createHeader();
            fputs($smtp, $headers . "\r\n");
            fputs($smtp, $this->Body . "\r\n");
            fputs($smtp, ".\r\n");

            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '250') {
                throw new Exception("Erreur envoi: $response");
            }

            // QUIT
            fputs($smtp, "QUIT\r\n");
            fclose($smtp);

            return true;
        } catch (Exception $e) {
            $this->ErrorInfo = $e->getMessage();
            if (isset($smtp) && is_resource($smtp)) {
                fclose($smtp);
            }
            return false;
        }
    }
    
    public function addAddress($address, $name = '') {
        $this->to[] = array($address, $name);
    }
    
    public function setFrom($address, $name = '') {
        $this->From = $address;
        $this->FromName = $name;
    }
    
    public function isHTML($isHtml = true) {
        // Cette méthode existe pour la compatibilité
    }
    
    protected function createHeader() {
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . $this->FromName . " <" . $this->From . ">";
        $headers[] = "Reply-To: " . $this->From;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $headers[] = "Subject: " . $this->Subject;
        
        return implode("\r\n", $headers);
    }
}
