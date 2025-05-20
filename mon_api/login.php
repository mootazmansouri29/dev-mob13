<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once('dbconnect.php');

// Récupère le JSON brut envoyé par Flutter
$data = json_decode(file_get_contents("php://input"));

// Vérifie que les champs existent
$email = $data->email ?? '';
$password = $data->password ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email ou mot de passe manquant.']);
    exit;
}

// Prépare et exécute la requête SQL
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifie le mot de passe
if ($user && password_verify($password, $user['password'])) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'roles' => json_decode($user['roles'])
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
}
?>
