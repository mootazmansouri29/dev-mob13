<?php
require_once('dbconnect.php');
header('Content-Type: application/json');

// Lire les données JSON envoyées par Flutter
$data = json_decode(file_get_contents("php://input"), true);

// Vérifier que toutes les données nécessaires sont présentes
if (
    isset($data['email']) &&
    isset($data['password']) &&
    isset($data['name']) &&
    isset($data['roles'])
) {
    // Récupération et sécurisation des données
    $email = $data['email'];
    $name = $data['name'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash du mot de passe
    $roleKey = strtolower(trim($data['roles'])) === 'organizer' ? 'ROLE_ORGANIZER' : 'ROLE_USER';
    $roles = json_encode([$roleKey]);
    $profileImage = isset($data['profile_image']) ? $data['profile_image'] : '';
    $favoriteCategories = isset($data['favorite_categories']) ? $data['favorite_categories'] : '';
    $registeredEvents = ''; // Vide par défaut à la création

    try {
        // Vérifier si l'utilisateur existe déjà
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Un utilisateur avec cet email existe déjà.'
            ]);
            exit;
        }

        // Préparer la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO users 
            (email, name, password, roles, profile_image, favorite_categories, registered_events) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $email,
            $name,
            $password,
            $roles,
            $profileImage,
            $favoriteCategories,
            $registeredEvents
        ]);

        echo json_encode(['success' => true, 'message' => 'Utilisateur enregistré avec succès.']);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Champs requis manquants.'
    ]);
}
