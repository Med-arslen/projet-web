<?php
require_once '../config/Database.php';
require_once '../services/QRCodeService.php';

if (!isset($_GET['user_id'])) {
    die('ID utilisateur manquant');
}

$userId = (int)$_GET['user_id'];
$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Utilisateur non trouvé');
}

// Générer le QR code
$qrService = new QRCodeService();
$qrData = [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'],
    'phone' => $user['phone'] ?? ''
];
$qrFileName = $qrService->generateQRCode($userId, $qrData);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - <?= htmlspecialchars($user['name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #000000;
            margin: 0;
            padding: 20px;
            color: #ffffff;
        }
        
        .qr-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #ff0000;
        }
        
        .qr-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .qr-header h2 {
            color: #ff0000;
            margin: 0;
            font-size: 24px;
        }
        
        .qr-image-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            width: fit-content;
            border: 1px solid #ff0000;
        }
        
        .qr-frame {
            width: 300px;
            height: 300px;
            border: none;
            overflow: hidden;
        }
        
        .user-info {
            text-align: left;
            background-color: #000000;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #ff0000;
        }
        
        .user-info p {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        
        .user-info i {
            width: 24px;
            margin-right: 10px;
            color: #ff0000;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            background-color: #ff0000;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #cc0000;
        }
        
        .btn-secondary {
            background-color: #000000;
            border: 1px solid #ff0000;
        }
        
        .btn-secondary:hover {
            background-color: #ff0000;
        }
        
        .info-message {
            background-color: #000000;
            border: 1px solid #ff0000;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
            color: #ff0000;
        }

        @media print {
            body {
                background-color: white;
            }
            .qr-container {
                border: none;
                padding: 0;
            }
            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="qr-header">
            <h2>QR Code - <?= htmlspecialchars($user['name']) ?></h2>
            <p>Scannez ce QR code pour ajouter ce contact à votre carnet d'adresses</p>
        </div>
        
        <div class="qr-image-container">
            <iframe src="/finprojet/assets/qrcodes/<?= htmlspecialchars($qrFileName) ?>" 
                    class="qr-frame"
                    id="qrFrame"></iframe>
        </div>
        
        <div class="user-info">
            <p><i class="fas fa-id-card"></i> <strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
            <p><i class="fas fa-user"></i> <strong>Nom:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><i class="fas fa-user-tag"></i> <strong>Rôle:</strong> <?= htmlspecialchars($user['role']) ?></p>
            <?php if (!empty($user['phone'])): ?>
                <p><i class="fas fa-phone"></i> <strong>Téléphone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="info-message">
            <i class="fas fa-info-circle"></i> 
            Ce QR code contient vos informations de contact et peut être scanné pour les ajouter à un carnet d'adresses
        </div>
        
        <div class="action-buttons">
            <button onclick="captureQR()" class="btn">
                <i class="fas fa-download"></i> Télécharger
            </button>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
    function captureQR() {
        const frame = document.querySelector('.qr-image-container');
        html2canvas(frame).then(canvas => {
            const link = document.createElement('a');
            link.download = 'qr_<?= htmlspecialchars($user['name']) ?>.png';
            link.href = canvas.toDataURL();
            link.click();
        });
    }

    // Vérifier si le QR code est chargé correctement
    document.getElementById('qrFrame').onerror = function() {
        alert('Erreur lors de la génération du QR code. Veuillez réessayer.');
    };
    </script>
</body>
</html>