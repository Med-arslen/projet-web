<?php
include_once __DIR__ . '/../Model/feedback.php';
include_once __DIR__ . '/../config/database.php';

class feedbackController {

    public function showFeedback($id) {
        $sql = "SELECT * FROM feedbackk WHERE id_fed = :id_fed";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':id_fed' => $id]);
            $feedback = $query->fetch(PDO::FETCH_ASSOC);
            return $feedback;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function listFeedback() {
        $sql = "SELECT * FROM feedbackk";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $liste = $query->fetchAll(PDO::FETCH_ASSOC);
            return $liste;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addFeedback($feedbackk) {
        $sql = "INSERT INTO feedbackk (id_fed, id_rec, analyse, conseil, simplicite, temps)
                VALUES (:id_fed, :id_rec, :analyse, :conseil, :simplicite, :temps)";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $success = $query->execute([
                'id_fed'    => $feedbackk->getIdFed(),
                'id_rec'    => $feedbackk->getIdRec(),
                'analyse'   => $feedbackk->getAnalyse(),
                'conseil'   => $feedbackk->getConseil(),
                'simplicite'=> $feedbackk->getSimplicite(),
                'temps'     => $feedbackk->getTemps()
            ]);
            
            if (!$success) {
                throw new Exception("Erreur lors de l'insertion du feedback");
            }
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur : " . $e->getMessage());
        }
    }

    public function updateFeedback($feedbackk, $id) {
        $sql = "UPDATE feedbackk SET 
                    analyse = :analyse,
                    conseil = :conseil,
                    id_rec = :id_rec,
                    simplicite = :simplicite,  -- Mise à jour du champ 'simplicite'
                    temps = :temps             -- Mise à jour du champ 'temps'
                WHERE id_fed = :id_fed";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_fed'    => $id,
                'analyse'   => $feedbackk->getAnalyse(),
                'conseil'   => $feedbackk->getConseil(),
                'id_rec'    => $feedbackk->getIdRec(),
                'simplicite'=> $feedbackk->getSimplicite(),  // Envoi du champ 'simplicite'
                'temps'     => $feedbackk->getTemps()         // Envoi du champ 'temps'
            ]);
            echo $query->rowCount() . " feedbackk updated successfully.<br>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function deleteFeedback($id_rec) {
        $sql = "DELETE FROM feedbackk WHERE id_rec = :id_rec";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_rec', $id_rec, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Nouvelle fonction pour obtenir les statistiques sur le temps
    public function getTempsStats() {
        $sql = "SELECT temps, COUNT(*) as count FROM feedbackk GROUP BY temps";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $stats = [
                'rapide' => 0,
                'moyen' => 0,
                'lent' => 0
            ];

            // Remplir les statistiques avec les résultats de la requête
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $temps = $row['temps'];
                if (isset($stats[$temps])) {
                    $stats[$temps] = $row['count'];
                }
            }

            return $stats;

        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    public static function traduction_form($texte, $lang) {
        $traductions = [
            'fr' => [
                'Accueil' => 'Accueil',
                'Catalogue' => 'Catalogue',
                'Événements' => 'Événements',
                'Achat' => 'Achat',
                'Réclamation' => 'Réclamation',
                'Historique' => 'Historique',
                'Merci Pour Votre Réclamation !' => 'Merci Pour Votre Réclamation !',
                'Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :' => 
                    'Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :',
                'Notez notre service :' => 'Notez notre service :',
                'Veuillez sélectionner une note.' => 'Veuillez sélectionner une note.',
                'Le processus de réclamation était-il simple ?' => 'Le processus de réclamation était-il simple ?',
                'Oui' => 'Oui',
                'Non' => 'Non',
                'Veuillez répondre à cette question.' => 'Veuillez répondre à cette question.',
                'Le temps de réponse vous semble-t-il raisonnable ?' => 'Le temps de réponse vous semble-t-il raisonnable ?',
                'Rapide' => 'Rapide',
                'Moyen' => 'Moyen',
                'Lent' => 'Lent',
                'Un commentaire à ajouter ?' => 'Un commentaire à ajouter ?',
                'Dites-nous ce que vous pensez du service...' => 'Dites-nous ce que vous pensez du service...',
                'Envoyer le feedback' => 'Envoyer le feedback',
                'Veuillez sélectionner une note' => 'Veuillez sélectionner une note',
                'Veuillez répondre à cette question' => 'Veuillez répondre à cette question',
                'Une erreur est survenue' => 'Une erreur est survenue',
                'Envoi en cours...' => 'Envoi en cours...',
                'Mentions légales' => 'Mentions légales',
                'Veuillez ajouter un commentaire' => 'Veuillez ajouter un commentaire',
                'Tous les champs sont requis' => 'Tous les champs sont requis',
                'Format d\'email invalide' => 'Format d\'email invalide',
                'Le nom et prénom sont requis' => 'Le nom et prénom sont requis',
                'Veuillez remplir tous les champs' => 'Veuillez remplir tous les champs',
                'Une erreur est survenue lors de l\'enregistrement' => 'Une erreur est survenue lors de l\'enregistrement',
                'Erreur réseau' => 'Erreur réseau',
                'Une erreur inattendue s\'est produite' => 'Une erreur inattendue s\'est produite',
                'Merci pour votre feedback !' => 'Merci pour votre feedback !',
                'Nous apprécions grandement votre contribution qui nous aidera à améliorer nos services.' => 'Nous apprécions grandement votre contribution qui nous aidera à améliorer nos services.',
                'Retour à l\'accueil' => 'Retour à l\'accueil',
            ],
            'en' => [
                'Accueil' => 'Home',
                'Catalogue' => 'Catalog',
                'Événements' => 'Events',
                'Achat' => 'Purchase',
                'Réclamation' => 'Complaint',
                'Historique' => 'History',
                'Merci Pour Votre Réclamation !' => 'Thank you for your complaint!',
                'Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :' =>
                    'We have received your request. Help us improve by answering this short survey:',
                'Notez notre service :' => 'Rate our service:',
                'Veuillez sélectionner une note.' => 'Please select a rating.',
                'Le processus de réclamation était-il simple ?' => 'Was the complaint process simple?',
                'Oui' => 'Yes',
                'Non' => 'No',
                'Veuillez répondre à cette question.' => 'Please answer this question.',
                'Le temps de réponse vous semble-t-il raisonnable ?' => 'Was the response time reasonable?',
                'Rapide' => 'Fast',
                'Moyen' => 'Average',
                'Lent' => 'Slow',
                'Un commentaire à ajouter ?' => 'Any comments?',
                'Dites-nous ce que vous pensez du service...' => 'Tell us what you think about the service...',
                'Envoyer le feedback' => 'Send feedback',
                'Veuillez sélectionner une note' => 'Please select a rating',
                'Veuillez répondre à cette question' => 'Please answer this question',
                'Une erreur est survenue' => 'An error occurred',
                'Envoi en cours...' => 'Sending...',
                'Mentions légales' => 'Legal notice',
                'Veuillez ajouter un commentaire' => 'Please add a comment',
                'Tous les champs sont requis' => 'All fields are required',
                'Format d\'email invalide' => 'Invalid email format',
                'Le nom et prénom sont requis' => 'First and last name are required',
                'Veuillez remplir tous les champs' => 'Please fill in all fields',
                'Une erreur est survenue lors de l\'enregistrement' => 'An error occurred during submission',
                'Erreur réseau' => 'Network error',
                'Une erreur inattendue s\'est produite' => 'An unexpected error occurred',
                'Merci pour votre feedback !' => 'Thank you for your feedback!',
                'Nous apprécions grandement votre contribution qui nous aidera à améliorer nos services.' => 'We greatly appreciate your contribution which will help us improve our services.',
                'Retour à l\'accueil' => 'Back to home',
            ],
            'es' => [
                'Accueil' => 'Inicio',
                'Catalogue' => 'Catálogo',
                'Événements' => 'Eventos',
                'Achat' => 'Compra',
                'Réclamation' => 'Reclamación',
                'Historique' => 'Historial',
                'Merci Pour Votre Réclamation !' => '¡Gracias por su reclamación!',
                'Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :' =>
                    'Hemos recibido su solicitud. Ayúdanos a mejorar respondiendo esta breve encuesta:',
                'Notez notre service :' => 'Califique nuestro servicio:',
                'Veuillez sélectionner une note.' => 'Por favor seleccione una calificación.',
                'Le processus de réclamation était-il simple ?' => '¿El proceso fue sencillo?',
                'Oui' => 'Sí',
                'Non' => 'No',
                'Veuillez répondre à cette question.' => 'Por favor responda esta pregunta.',
                'Le temps de réponse vous semble-t-il raisonnable ?' => '¿El tiempo de respuesta fue razonable?',
                'Rapide' => 'Rápido',
                'Moyen' => 'Promedio',
                'Lent' => 'Lento',
                'Un commentaire à ajouter ?' => '¿Desea agregar un comentario?',
                'Dites-nous ce que vous pensez du service...' => 'Cuéntenos qué piensa del servicio...',
                'Envoyer le feedback' => 'Enviar comentarios',
                'Veuillez sélectionner une note' => 'Por favor seleccione una calificación',
                'Veuillez répondre à cette question' => 'Por favor responda esta pregunta',
                'Une erreur est survenue' => 'Se ha producido un error',
                'Envoi en cours...' => 'Enviando...',
                'Mentions légales' => 'Aviso legal',
                'Veuillez ajouter un commentaire' => 'Por favor, añada un comentario',
                'Tous les champs sont requis' => 'Todos los campos son obligatorios',
                'Format d\'email invalide' => 'Formato de correo electrónico inválido',
                'Le nom et prénom sont requis' => 'Nombre y apellido son obligatorios',
                'Veuillez remplir tous les champs' => 'Por favor complete todos los campos',
                'Une erreur est survenue lors de l\'enregistrement' => 'Ocurrió un error durante el envío',
                'Erreur réseau' => 'Error de red',
                'Une erreur inattendue s\'est produite' => 'Ocurrió un error inesperado',
                'Merci pour votre feedback !' => '¡Gracias por sus comentarios!',
                'Nous apprécions grandement votre contribution qui nous aidera à améliorer nos services.' => 'Apreciamos enormemente su contribución que nos ayudará a mejorar nuestros servicios.',
                'Retour à l\'accueil' => 'Volver al inicio',
            ],
            'ar' => [
                'Accueil' => 'الرئيسية',
                'Catalogue' => 'الفهرس',
                'Événements' => 'الفعاليات',
                'Achat' => 'الشراء',
                'Réclamation' => 'شكوى',
                'Historique' => 'السجل',
                'Merci Pour Votre Réclamation !' => 'شكرًا على شكواك!',
                'Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :' =>
                    'لقد استلمنا طلبك. ساعدنا على تحسين خدمتنا بالإجابة على هذا الاستبيان القصير:',
                'Notez notre service :' => 'قيّم خدمتنا:',
                'Veuillez sélectionner une note.' => 'يرجى اختيار تقييم.',
                'Le processus de réclamation était-il simple ?' => 'هل كانت عملية الشكوى بسيطة؟',
                'Oui' => 'نعم',
                'Non' => 'لا',
                'Veuillez répondre à cette question.' => 'يرجى الإجابة على هذا السؤال.',
                'Le temps de réponse vous semble-t-il raisonnable ?' => 'هل كان وقت الاستجابة مناسبًا؟',
                'Rapide' => 'سريع',
                'Moyen' => 'متوسط',
                'Lent' => 'بطيء',
                'Un commentaire à ajouter ?' => 'هل لديك تعليق؟',
                'Dites-nous ce que vous pensez du الخدمة...' => 'أخبرنا برأيك حول الخدمة...',
                'Envoyer le feedback' => 'إرسال التعليق',
                'Veuillez sélectionner une note' => 'الرجاء اختيار تقييم',
                'Veuillez répondre إلى cette question' => 'الرجاء الإجابة على هذا السؤال',
                'Une erreur est survenue' => 'حدث خطأ',
                'Envoi en cours...' => 'جاري الإرسال...',
                'Mentions légales' => 'إشعار قانوني',
                'Veuillez ajouter un commentaire' => 'الرجاء إضافة تعليق',
                'Tous les champs sont requis' => 'جميع الحقول مطلوبة',
                'Format d\'email invalide' => 'تنسيق البريد الإلكتروني غير صالح',
                'Le nom et prénom sont requis' => 'الاسم الكامل مطلوب',
                'Veuillez remplir tous les champs' => 'يرجى ملء جميع الحقول',
                'Une erreur est survenue lors de l\'enregistrement' => 'حدث خطأ أثناء الإرسال',
                'Erreur réseau' => 'خطأ في الشبكة',
                'Une erreur inattendue s\'est produite' => 'حدث خطأ غير متوقع',
                'Merci pour votre feedback !' => '!شكراً على ملاحظاتك',
                'Nous apprécions grandement votre contribution qui nous aidera à améliorer nos services.' => 'نقدر مساهمتك التي ستساعدنا في تحسين خدماتنا',
                'Retour à l\'accueil' => 'العودة إلى الصفحة الرئيسية',
            ]
        ];
    
        return $traductions[$lang][$texte] ?? $texte;
    }
    
    public function generatePDF() {
        require_once __DIR__ . '/../lib/dompdf/dompdf/autoload.inc.php';
        
        $feedbacks = $this->listFeedback();
        
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
                <h1>Liste des Feedback - MovieVibe</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID Feedback</th>
                        <th>Analyse</th>
                        <th>Conseil</th>
                        <th>Temps</th>
                        <th>Simplicité</th>
                        <th>ID Réclamation</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($feedbacks as $feedback) {
            $html .= '<tr>
                <td>' . htmlspecialchars($feedback['id_fed']) . '</td>
                <td>' . htmlspecialchars($feedback['analyse']) . '</td>
                <td>' . htmlspecialchars($feedback['conseil']) . '</td>
                <td>' . htmlspecialchars($feedback['temps']) . '</td>
                <td>' . htmlspecialchars($feedback['simplicite']) . '</td>
                <td>' . htmlspecialchars($feedback['id_rec']) . '</td>
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
}
?>
