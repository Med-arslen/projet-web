<?php
class QRCodeService {
    private $savePath;
    
    public function __construct() {
        $this->savePath = __DIR__ . '/../assets/qrcodes/';
        if (!file_exists($this->savePath)) {
            mkdir($this->savePath, 0777, true);
        }
    }
    
    public function generateQRCode($userId, $data) {
        // Générer une chaîne unique pour l'utilisateur
        $userData = json_encode($data);
        $uniqueString = base64_encode($userData);
        
        // Créer le contenu HTML du QR code
        $qrHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code</title>
            <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
        </head>
        <body style="margin:0;display:flex;justify-content:center;align-items:center;background:white;">
            <div id="qrcode"></div>
            <script>
            new QRCode(document.getElementById("qrcode"), {
                text: "' . $uniqueString . '",
                width: 256,
                height: 256
            });
            </script>
        </body>
        </html>';
        
        // Sauvegarder le fichier HTML
        $filename = "qr_" . $userId . ".html";
        $filepath = $this->savePath . $filename;
        file_put_contents($filepath, $qrHtml);
        
        return $filename;
    }
    
    public function getQRCodeUrl($userId) {
        return '/finprojet/assets/qrcodes/qr_' . $userId . '.html';
    }
}