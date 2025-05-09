<?php
include_once __DIR__ . '/../Model/Reclamation.php';
include_once __DIR__ . '/../config/database.php';

class ReclamationController {
    // Afficher une réclamation
    public function showReclamation($id)
    {
        // Vérification si l'ID est passé et valide
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

    // Lister toutes les réclamations
    public function listReclamations()
    {
        $sql = "SELECT * FROM reclamationn";
        $db = config::getConnexion();
        try {
            return $db->query($sql);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des réclamations : " . $e->getMessage());
            die('Erreur lors de la récupération des réclamations.');
        }
    }

    // Mettre à jour une réclamation
    public function updateReclamation($reclamationn, $id)
    {
        // Vérification si l'ID est valide
        if (empty($id) || !is_numeric($id)) {
            die('ID de réclamation invalide.');
        }

        try {
            // Validation des données avant de les exécuter
            if (empty($reclamationn->getNomPrenom()) || empty($reclamationn->getEmail()) || empty($reclamationn->getNomFilm()) || empty($reclamationn->getTypeRec()) || empty($reclamationn->getDetail()) || empty($reclamationn->getReponseRec())) {
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
                'id_rec'    => $id,
                'nomprenom' => $reclamationn->getNomPrenom(),
                'email'     => $reclamationn->getEmail(),
                'nomfilm'   => $reclamationn->getNomFilm(),
                'type_rec'  => $reclamationn->getTypeRec(),
                'detail'    => $reclamationn->getDetail(),
                'reponse_rec'    => $reclamationn->getReponseRec()
            ]);

            // Message de succès
            echo "Réclamation mise à jour avec succès.";
            
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la réclamation : " . $e->getMessage());
            echo "Erreur lors de la mise à jour de la réclamation : " . $e->getMessage();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Ajouter une nouvelle réclamation
    public function addReclamation($reclamationn)
    {
        $sql = "INSERT INTO reclamationn (nomprenom,email,nomfilm,type_rec, detail, reponse_rec)
                VALUES (:nomprenom, :email, :nomfilm, :type_rec, :detail , :reponse_rec)";
    
        $db = config::getConnexion();
    
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'nomprenom'   => $reclamationn->getNomPrenom(),
                'email'       => $reclamationn->getEmail(),
                'nomfilm'     => $reclamationn->getNomFilm(),
                'type_rec'    => $reclamationn->getTypeRec(),
                'detail'      => $reclamationn->getDetail(),
                'reponse_rec' => $reclamationn->getReponseRec()
            ]);

            if ($result) {
                $id_rec = $db->lastInsertId();
                return json_encode([
                    'success' => true,
                    'data' => ['id_rec' => $id_rec]
                ]);
            } else {
                return json_encode([
                    'success' => false,
                    'message' => "Une erreur est survenue lors de l'enregistrement."
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => "Erreur : " . $e->getMessage()
            ]);
        }
    }

    // Supprimer une réclamation
    public function deleteReclamation($id)
    {
        // Vérification si l'ID est valide
        if (empty($id) || !is_numeric($id)) {
            throw new Exception('ID de réclamation invalide.');
        }

        $sql = "DELETE FROM reclamationn WHERE id_rec = :id_rec";
        $db = config::getConnexion();
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_rec', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            if ($result === false) {
                throw new Exception('Échec de la suppression de la réclamation.');
            }
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Aucune réclamation trouvée avec cet ID.');
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la réclamation : " . $e->getMessage());
            throw new Exception('Erreur de suppression : ' . $e->getMessage());
        }
    }

    public function getReclamationsSorted($sort_order = 'DESC')
    {
        // Connexion à la base de données avec PDO
        $db = config::getConnexion();
    
        // Création de la requête SQL pour trier par ordre décroissant ou ascendant selon le paramètre
        $sql = "SELECT * FROM reclamationn ORDER BY id_rec " . strtoupper($sort_order);
    
        try {
            // Préparer et exécuter la requête
            $query = $db->prepare($sql);
            $query->execute();
    
            // Récupérer et retourner les résultats sous forme de tableau associatif
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log l'erreur et affiche un message détaillé si la requête échoue
            error_log("Erreur lors de la récupération des réclamations triées : " . $e->getMessage());
            die('Erreur lors de la récupération des réclamations triées. Détails : ' . $e->getMessage());
        }
    }
    public static function getStatistiquesTypeRec($conn)
    {
        $sql = "SELECT type_rec, COUNT(*) as total FROM reclamationn GROUP BY type_rec";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getStatistiquesReponseRec()
{
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

public static function traduction_form($text, $lang) {
    $translations = [
        'fr' => [
            'Formulaire de Réclamation' => 'Formulaire de Réclamation',
            'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'Veuillez remplir ce formulaire pour nous signaler un problème technique.',
            'Veuillez remplir ce formulaire pour signaler un problème.' => 'Veuillez remplir ce formulaire pour signaler un problème.',
            'Nom Prénom :' => 'Nom Prénom :',
            'Email :' => 'Email :',
            'Titre du film concerné :' => 'Titre du film concerné :',
            'Type de problème :' => 'Type de problème :',
            'Sélectionnez un problème' => 'Sélectionnez un problème',
            'Lien cassé' => 'Lien cassé',
            'Qualité mauvaise' => 'Qualité mauvaise',
            'Langue audio/sous-titre incorrecte' => 'Langue audio/sous-titre incorrecte',
            'Autre' => 'Autre',
            'Détails du problème :' => 'Détails du problème :',
            'Décrivez le problème en détail...' => 'Décrivez le problème en détail...',
            'Envoyer la réclamation' => 'Envoyer la réclamation',
            'Accueil' => 'Accueil',
            'Catalogue' => 'Catalogue',
            'Événements' => 'Événements',
            'Achat' => 'Achat',
            'Réclamation' => 'Réclamation',
            'Historique' => 'Historique',
            'Le nom et prénom sont requis' => 'Le nom et prénom sont requis',
            'L\'email est requis' => 'L\'email est requis',
            'Format d\'email invalide' => 'Format d\'email invalide',
            'Le nom du film est requis' => 'Le nom du film est requis',
            'Le type de réclamation est requis' => 'Le type de réclamation est requis',
            'Les détails sont requis' => 'Les détails sont requis',
            'Envoi en cours...' => 'Envoi en cours...',
        ],
        'en' => [
            'Formulaire de Réclamation' => 'Complaint Form',
            'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'Please fill out this form to report a technical issue.',
            'Veuillez remplir ce formulaire pour signaler un problème.' => 'Please fill out this form to report an issue.',
            'Nom Prénom :' => 'Full Name:',
            'Email :' => 'Email:',
            'Titre du film concerné :' => 'Movie Title:',
            'Type de problème :' => 'Issue Type:',
            'Sélectionnez un problème' => 'Select an issue',
            'Lien cassé' => 'Broken link',
            'Qualité mauvaise' => 'Poor quality',
            'Langue audio/sous-titre incorrecte' => 'Incorrect audio/subtitle language',
            'Autre' => 'Other',
            'Détails du problème :' => 'Problem details:',
            'Décrivez le problème en détail...' => 'Describe the issue in detail...',
            'Envoyer la réclamation' => 'Submit complaint',
            'Accueil' => 'Home',
            'Catalogue' => 'Catalog',
            'Événements' => 'Events',
            'Achat' => 'Purchase',
            'Réclamation' => 'Complaint',
            'Historique' => 'History',
            'Le nom et prénom sont requis' => 'Full name is required',
            'L\'email est requis' => 'Email is required',
            'Format d\'email invalide' => 'Invalid email format',
            'Le nom du film est requis' => 'Movie title is required',
            'Le type de réclamation est requis' => 'Complaint type is required',
            'Les détails sont requis' => 'Details are required',
            'Envoi en cours...' => 'Sending...',
        ],
        'es' => [
            'Formulaire de Réclamation' => 'Formulario de Reclamación',
            'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'Por favor, complete este formulario para reportar un problema técnico.',
            'Veuillez remplir ce formulaire pour signaler un problème.' => 'Por favor, complete este formulario para reportar un problema.',
            'Nom Prénom :' => 'Nombre Completo:',
            'Email :' => 'Correo Electrónico:',
            'Titre du film concerné :' => 'Título de la Película:',
            'Type de problème :' => 'Tipo de Problema:',
            'Sélectionnez un problème' => 'Seleccione un problema',
            'Lien cassé' => 'Enlace roto',
            'Qualité mauvaise' => 'Calidad deficiente',
            'Langue audio/sous-titre incorrecte' => 'Idioma de audio/subtítulos incorrecto',
            'Autre' => 'Otro',
            'Détails du problème :' => 'Detalles del problema:',
            'Décrivez le problème en détail...' => 'Describa el problema con detalle...',
            'Envoyer la réclamation' => 'Enviar la reclamación',
            'Accueil' => 'Inicio',
            'Catalogue' => 'Catálogo',
            'Événements' => 'Eventos',
            'Achat' => 'Compra',
            'Réclamation' => 'Reclamación',
            'Historique' => 'Historial',
            'Le nom et prénom sont requis' => 'El nombre completo es obligatorio',
            'L\'email est requis' => 'El correo electrónico es obligatorio',
            'Format d\'email invalide' => 'Formato de correo electrónico inválido',
            'Le nom du film est requis' => 'El título de la película es obligatorio',
            'Le type de réclamation est requis' => 'El tipo de reclamación es obligatorio',
            'Les détails sont requis' => 'Los detalles son obligatorios',
            'Envoi en cours...' => 'Enviando...',
        ],
        'ar' => [
            'Formulaire de Réclamation' => 'نموذج الشكوى',
            'Veuillez remplir ce formulaire pour nous signaler un problème technique.' => 'يرجى ملء هذا النموذج للإبلاغ عن مشكلة تقنية.',
            'Veuillez remplir ce formulaire pour signaler un problème.' => 'يرجى ملء هذا النموذج للإبلاغ عن مشكلة.',
            'Nom Prénom :' => 'الاسم الكامل:',
            'Email :' => 'البريد الإلكتروني:',
            'Titre du film concerné :' => 'عنوان الفيلم:',
            'Type de problème :' => 'نوع المشكلة:',
            'Sélectionnez un problème' => 'اختر مشكلة',
            'Lien cassé' => 'رابط معطل',
            'Qualité mauvaise' => 'جودة سيئة',
            'Langue audio/sous-titre incorrecte' => 'لغة الصوت/الترجمة غير صحيحة',
            'Autre' => 'أخرى',
            'Détails du problème :' => 'تفاصيل المشكلة:',
            'Décrivez le problème en détail...' => 'يرجى وصف المشكلة بالتفصيل...',
            'Envoyer la réclamation' => 'إرسال الشكوى',
            'Accueil' => 'الرئيسية',
            'Catalogue' => 'الفهرس',
            'Événements' => 'الفعاليات',
            'Achat' => 'الشراء',
            'Réclamation' => 'الشكوى',
            'Historique' => 'السجل',
            'Le nom et prénom sont requis' => 'الاسم الكامل مطلوب',
            'L\'email est requis' => 'البريد الإلكتروني مطلوب',
            'Format d\'email invalide' => 'تنسيق البريد الإلكتروني غير صالح',
            'Le nom du film est requis' => 'عنوان الفيلم مطلوب',
            'Le type de réclamation est requis' => 'نوع الشكوى مطلوب',
            'Les détails sont requis' => 'التفاصيل مطلوبة',
            'Envoi en cours...' => 'جاري الإرسال...',
        ],
    ];

    return $translations[$lang][$text] ?? $text;
}
// ReclamationController.php

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




}

    
    
    

?>
