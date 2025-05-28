<?php
require_once './config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $motdepasse = $_POST['motdepasse'];
    $role = 'etudiant';

    if ($prenom && $nom && $email && $motdepasse) {
        $pdo = Database::getConnection();

        // Vérifier si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE courriel = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $message = "Un compte existe déjà avec cet email.";
        } else {
            // Insertion dans la base
            $stmt = $pdo->prepare("INSERT INTO etudiants (prenom, nom, courriel, motdepasse, role) VALUES (:prenom, :nom, :email, :motdepasse, :role)");
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':motdepasse', md5($motdepasse)); // à remplacer par password_hash en prod
            $stmt->bindParam(':role', $role);
            $stmt->execute();

            $message = "Compte créé avec succès !";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer un compte</title>
</head>
<body>
    <h2>Inscription Étudiant</h2>
    <form method="POST">
        <input type="text" name="prenom" placeholder="Prénom" required><br><br>
        <input type="text" name="nom" placeholder="Nom" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="motdepasse" placeholder="Mot de passe" required><br><br>
        <button type="submit">Créer un compte</button>
    </form>

    <p style="color:red;"><?= $message ?></p>
</body>
</html>
