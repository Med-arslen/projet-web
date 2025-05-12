<?php
include_once __DIR__ . '/../Model/Reclamation.php';
include_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/PHPMailer.php';

class ReclamationController {

    public function showReclamation($id) {
        if (empty($id) || !is_numeric($id)) {
            die('ID de réclamation invalide.');
        }

        $sql = "SELECT * FROM reclamationn WHERE id_rec = :id_rec";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([':id_rec' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de l'affichage de la réclamation : " . $e->getMessage());
            die('Erreur lors de la récupération de la réclamation.');
        }
    }

    public function listReclamations() {
        $sql = "SELECT * FROM reclamationn";
        $db = config::getConnexion();

        try {
            return $db->query($sql);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des réclamations : " . $e->getMessage());
            die('Erreur lors de la récupération des réclamations.');
        }
    }

    public function updateReclamation($reclamationn, $id) {
        if (empty($id) || !is_numeric($id)) {
            die('ID de réclamation invalide.');
        }

        try {
            if (
                empty($reclamationn->getNomPrenom()) ||
                empty($reclamationn->getEmail()) ||
                empty($reclamationn->getNomFilm()) ||
                empty($reclamationn->getTypeRec()) ||
                empty($reclamationn->getDetail()) ||
                empty($reclamationn->getReponseRec())
            ) {
                throw new Exception("Tous les champs doivent être remplis.");
            }

            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE reclamationn SET 
                    nomprenom = :nomprenom,
                    email = :email,
                    nomfilm = :nomfilm,
                    type_rec = :type_rec,
                    detail = :detail,
                    reponse_rec = :reponse_rec
                 WHERE id_rec = :id_rec'
            );

            $query->execute([
                'id_rec'       => $id,
                'nomprenom'    => $reclamationn->getNomPrenom(),
                'email'        => $reclamationn->getEmail(),
                'nomfilm'      => $reclamationn->getNomFilm(),
                'type_rec'     => $reclamationn->getTypeRec(),
                'detail'       => $reclamationn->getDetail(),
                'reponse_rec'  => $reclamationn->getReponseRec()
            ]);

            echo "Réclamation mise à jour avec succès.";
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour : " . $e->getMessage());
            echo "Erreur de mise à jour : " . $e->getMessage();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    public function addReclamation($reclamationn) {
        $sql = "INSERT INTO reclamationn (nomprenom, email, nomfilm, type_rec, detail, reponse_rec)
                VALUES (:nomprenom, :email, :nomfilm, :type_rec, :detail, :reponse_rec)";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'nomprenom'    => $reclamationn->getNomPrenom(),
                'email'        => $reclamationn->getEmail(),
                'nomfilm'      => $reclamationn->getNomFilm(),
                'type_rec'     => $reclamationn->getTypeRec(),
                'detail'       => $reclamationn->getDetail(),
                'reponse_rec'  => $reclamationn->getReponseRec()
            ]);

            if ($result) {
                $id_rec = $db->lastInsertId();
                return json_encode([
                    'success' => true,
                    'data'    => ['id_rec' => $id_rec]
                ]);
            } else {
                return json_encode([
                    'success' => false,
                    'message' => "Erreur lors de l'enregistrement."
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => "Erreur : " . $e->getMessage()
            ]);
        }
    }

    public function deleteReclamation($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new Exception('ID de réclamation invalide.');
        }

        $sql = "DELETE FROM reclamationn WHERE id_rec = :id_rec";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_rec', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new Exception('Aucune réclamation trouvée avec cet ID.');
            }

            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            throw new Exception('Erreur de suppression : ' . $e->getMessage());
        }
    }

    public function getReclamationsSorted($sort_order = 'DESC') {
        $db = config::getConnexion();
        $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM reclamationn ORDER BY id_rec $sort_order";

        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur de tri : " . $e->getMessage());
            die('Erreur lors du tri des réclamations : ' . $e->getMessage());
        }
    }

    public static function getStatistiquesTypeRec($conn) {
        $sql = "SELECT type_rec, COUNT(*) as total FROM reclamationn GROUP BY type_rec";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStatistiquesReponseRec() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN reponse_rec IS NOT NULL AND reponse_rec != '' THEN 1 ELSE 0 END) AS avecReponse,
                    SUM(CASE WHEN reponse_rec IS NULL OR reponse_rec = '' THEN 1 ELSE 0 END) AS sansReponse
                FROM reclamationn";

        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function generatePDF() {
        require_once __DIR__ . '/../lib/dompdf/dompdf/autoload.inc.php';
        
        // Appel à une méthode pour récupérer les réclamations
        $reclamations = $this->listReclamations();
        
        // Configuration de DomPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
    
        // Chemin vers le logo
        $logoPath = __DIR__ . '/../View/backoffice/assets/img/logo.png';
        
        // Convertir l'image en base64
        $logoData = base64_encode(file_get_contents($logoPath));
        
        // Préparation du contenu HTML
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: Helvetica, Arial, sans-serif;
                    padding: 20px;
                }
                .logo-container {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 150px;
                    height: auto;
                }
                h1 { 
                    color: #333; 
                    text-align: center; 
                    margin-bottom: 20px; 
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px; 
                }
                .date { 
                    text-align: right; 
                    margin: 10px; 
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                }
                th { 
                    background-color: #333; 
                    color: white; 
                    padding: 10px; 
                }
                td { 
                    padding: 8px; 
                    border-bottom: 1px solid #ddd; 
                }
                tr:nth-child(even) { 
                    background-color: #f2f2f2; 
                }
                .footer { 
                    text-align: center; 
                    font-size: 12px; 
                    margin-top: 30px; 
                }
            </style>
        </head>
        <body>
            <div class="date">Date: ' . date('d/m/Y') . '</div>
            <div class="logo-container">
                <img src="data:image/png;base64,' . $logoData . '" class="logo" alt="MovieVibe Logo">
            </div>
            <div class="header">
                <h1>Liste des Réclamations - MovieVibe</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID Réclamation</th>
                        <th>Nom & Prénom</th>
                        <th>Email</th>
                        <th>Film</th>
                        <th>Type</th>
                        <th>Réponse</th>
                    </tr>
                </thead>
                <tbody>';
        
        // Remplir le tableau avec les réclamations
        foreach ($reclamations as $reclamation) {
            $html .= '<tr>
                <td>' . htmlspecialchars($reclamation['id_rec']) . '</td>
                <td>' . htmlspecialchars($reclamation['nomprenom']) . '</td>
                <td>' . htmlspecialchars($reclamation['email']) . '</td>
                <td>' . htmlspecialchars($reclamation['nomfilm']) . '</td>
                <td>' . htmlspecialchars($reclamation['type_rec']) . '</td>
                <td>' . htmlspecialchars($reclamation['reponse_rec']) . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
            </table>
            <div class="footer">
                <p>MovieVibe - Rapport généré le ' . date('d/m/Y à H:i') . '</p>
                <p>Page {PAGE_NUM} sur {PAGE_COUNT}</p>
            </div>
        </body>
        </html>';
        
        // Génération du PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Output du PDF
        return $dompdf->output();
    }
    public static function generateQRCodeData($conn, $id_rec) {
        try {
            $stmt = $conn->prepare("SELECT * FROM reclamationn WHERE id_rec = :id");
            $stmt->bindParam(':id', $id_rec);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return json_encode([
                    'success' => false,
                    'error' => 'Réclamation non trouvée'
                ]);
            }
            
            return json_encode([
                'success' => true,
                'reclamation' => $data
            ]);
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'error' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }
    public static function sendMail($id_rec) {
        try {
            // Logique pour envoyer l'email
            // Exemple d'envoi d'email via PHP
            $emailSent = true;  // Remplacez par la logique d'envoi réelle (comme PHPMailer)
    
            if ($emailSent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email envoyé avec succès'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Échec de l\'envoi de l\'email'
                ]);
            }
        } catch (Exception $e) {
            // Si une exception se produit, retourner l'erreur dans la réponse JSON
            echo json_encode([
                'success' => false,
                'error' => 'Une erreur est survenue: ' . $e->getMessage()
            ]);
        }
        exit();  // Terminer le script après avoir renvoyé la réponse JSON
    }
    public static function traduction_form($text, $lang) {
        $translations = [
            'fr' => [
                'Formulaire de Réclamation' => 'Formulaire de Réclamation',
                'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'Veuillez remplir ce formulaire pour nous signaler un problème technique.',
                'Nom Prénom :' => 'Nom Prénom :',
                'Email :' => 'Email :',
                'Titre du film concerné :' => 'Titre du film concerné :',
                'Type de problème :' => 'Type de problème :',
                'Détails du problème :' => 'Détails du problème :',
                'Envoyer la réclamation' => 'Envoyer la réclamation',
                'Accueil' => 'Accueil',
                'Catalogue' => 'Catalogue',
                'Nom est requis' => 'Le nom est requis.',
                'Email est requis' => 'L\'email est requis.',
                'Email invalide' => 'Email invalide.',
                'Titre du film est requis' => 'Le titre du film est requis.',
                'Détails sont requis' => 'Les détails sont requis.',
                'Événement' => 'Événement',
                'Réclamation' => 'Réclamation',
                'Historique' => 'Historique',
                'Achat' => 'Achat',
                'Événements' => 'Événements',
                'Sélectionnez votre problème' => 'Sélectionnez votre problème',
                'Lien cassé' => 'Lien cassé',
                'Qualité mauvaise' => 'Qualité mauvaise',
                'Autre' => 'Autre',
                'Langue audio/sous-titre incorrecte' => 'Langue audio/sous-titre incorrecte',
            ],
            'en' => [
                'Formulaire de Réclamation' => 'Complaint Form',
                'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'Please fill out this form to report a technical issue.',
                'Nom Prénom :' => 'Full Name:',
                'Email :' => 'Email:',
                'Titre du film concerné :' => 'Movie Title:',
                'Type de problème :' => 'Issue Type:',
                'Détails du problème :' => 'Problem details:',
                'Envoyer la réclamation' => 'Submit complaint',
                'Accueil' => 'Home',
                'Catalogue' => 'Catalog',
                'Nom est requis' => 'Name is required.',
                'Email est requis' => 'Email is required.',
                'Email invalide' => 'Invalid email.',
                'Titre du film est requis' => 'Movie title is required.',
                'Détails sont requis' => 'Details are required.',
                'Événement' => 'Event',
                'Réclamation' => 'Complaint',
                'Historique' => 'History',
                'Achat' => 'Purchase',
                'Événements' => 'Events',
                'Sélectionnez votre problème' => 'Select your issue',
                'Lien cassé' => 'Broken link',
                'Qualité mauvaise' => 'Poor quality',
                'Autre' => 'Other',
                'Langue audio/sous-titre incorrecte' => 'Incorrect audio/subtitle language',
            ],
            'ar' => [
                'Formulaire de Réclamation' => 'نموذج الشكوى',
                'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'يرجى ملء هذا النموذج للإبلاغ عن مشكلة فنية.',
                'Nom Prénom :' => 'الاسم الكامل:',
                'Email :' => 'البريد الإلكتروني:',
                'Titre du film concerné :' => 'عنوان الفيلم:',
                'Type de problème :' => 'نوع المشكلة:',
                'Détails du problème :' => 'تفاصيل المشكلة:',
                'Envoyer la réclamation' => 'إرسال الشكوى',
                'Accueil' => 'الصفحة الرئيسية',
                'Catalogue' => 'الفهرس',
                'Nom est requis' => 'الاسم مطلوب.',
                'Email est requis' => 'البريد الإلكتروني مطلوب.',
                'Email invalide' => 'البريد الإلكتروني غير صالح.',
                'Titre du film est requis' => 'عنوان الفيلم مطلوب.',
                'Détails sont requis' => 'التفاصيل مطلوبة.',
                'Événement' => 'حدث',
                'Réclamation' => 'شكوى',
                'Historique' => 'السجل',
                'Achat' => 'شراء',
                'Événements' => 'الأحداث',
                'Sélectionnez votre problème' => 'اختر مشكلتك',
                'Lien cassé' => 'رابط معطل',
                'Qualité mauvaise' => 'جودة سيئة',
                'Autre' => 'أخرى',
                'Langue audio/sous-titre incorrecte' => 'لغة الصوت أو الترجمة غير صحيحة',
            ]
        ];
    
        return $translations[$lang][$text] ?? $text;
    }
    public static function traduction_historique($text, $lang = 'fr') {
    $translations = [
        'fr' => [
            'Votre Historique de Réclamations' => 'Votre Historique de Réclamations',
            'Nom Prénom' => 'Nom Prénom',
            'Email' => 'Email',
            'Film Concerné' => 'Film Concerné',
            'Type de Problème' => 'Type de Problème',
            'Détails' => 'Détails',
            'Réponse' => 'Réponse',
            'Afficher / Masquer les Statistiques' => 'Afficher / Masquer les Statistiques',
            'Statistiques des Réponses' => 'Statistiques des Réponses',
            'Avec réponse' => 'Avec réponse',
            'Sans réponse' => 'Sans réponse',
            'Home' => 'Accueil',
            'Catalogue' => 'Catalogue',
            'Events' => 'Événements',
            'Achat' => 'Achat',
            'Réclamation' => 'Réclamation'
        ],
        'en' => [
            'Votre Historique de Réclamations' => 'Your Complaint History',
            'Nom Prénom' => 'Full Name',
            'Email' => 'Email',
            'Film Concerné' => 'Movie',
            'Type de Problème' => 'Issue Type',
            'Détails' => 'Details',
            'Réponse' => 'Response',
            'Afficher / Masquer les Statistiques' => 'Show / Hide Statistics',
            'Statistiques des Réponses' => 'Response Statistics',
            'Avec réponse' => 'With Response',
            'Sans réponse' => 'Without Response',
            'Home' => 'Home',
            'Catalogue' => 'Catalog',
            'Events' => 'Events',
            'Achat' => 'Purchase',
            'Réclamation' => 'Complaint'
        ],
        'es' => [
            'Votre Historique de Réclamations' => 'Tu Historial de Reclamaciones',
            'Nom Prénom' => 'Nombre y Apellido',
            'Email' => 'Correo electrónico',
            'Film Concerné' => 'Película',
            'Type de Problème' => 'Tipo de Problema',
            'Détails' => 'Detalles',
            'Réponse' => 'Respuesta',
            'Afficher / Masquer les Statistiques' => 'Mostrar / Ocultar Estadísticas',
            'Statistiques des Réponses' => 'Estadísticas de Respuestas',
            'Avec réponse' => 'Con Respuesta',
            'Sans réponse' => 'Sin Respuesta',
            'Home' => 'Inicio',
            'Catalogue' => 'Catálogo',
            'Events' => 'Eventos',
            'Achat' => 'Compra',
            'Réclamation' => 'Reclamación'
        ],
        'ar' => [
            'Votre Historique de Réclamations' => 'سجل الشكاوى الخاص بك',
            'Nom Prénom' => 'الاسم الكامل',
            'Email' => 'البريد الإلكتروني',
            'Film Concerné' => 'الفيلم المعني',
            'Type de Problème' => 'نوع المشكلة',
            'Détails' => 'التفاصيل',
            'Réponse' => 'الرد',
            'Afficher / Masquer les Statistiques' => 'إظهار / إخفاء الإحصائيات',
            'Statistiques des Réponses' => 'إحصائيات الردود',
            'Avec réponse' => 'مع رد',
            'Sans réponse' => 'بدون رد',
            'Home' => 'الرئيسية',
            'Catalogue' => 'الفهرس',
            'Events' => 'الفعاليات',
            'Achat' => 'شراء',
            'Réclamation' => 'شكوى'
        ]
    ];

    return $translations[$lang][$text] ?? $translations['fr'][$text] ?? $text;
}

}
