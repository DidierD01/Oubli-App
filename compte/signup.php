<?php
session_start();
require_once '../config/database.php';

$message = '';

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']); 
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    $data = "&nom=$nom&prenom=$prenom&email=$email";

    if (empty($nom)) {
        $message = "Le champ nom est requis";
    } elseif (empty($prenom)) {
        $message = "Le champ prénom est requis";
    } elseif (empty($email)) {
        $message = "Le champ email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "L'adresse email n'est pas valide";
    } elseif (empty($password)) {
        $message = "Le champ mot de passe est requis";
    } elseif ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas";
    } else {
        $database = new Database();
        $db = $database->getConnection();

        // Vérifier si l'email est déjà utilisé
        $sql_check_email = "SELECT user_email FROM tbl_users WHERE user_email = :email";
        $stmt = $db->prepare($sql_check_email);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $message = "Cette adresse email est déjà utilisée";
        } else {
            // Vérifier la sécurité du mot de passe
            $password_errors = [];

            if (strlen($password) < 8) {
                $password_errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            }
            if (!preg_match('/\d/', $password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins un chiffre.";
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins une lettre majuscule.";
            }
            if (!preg_match('/[a-z]/', $password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins une lettre minuscule.";
            }
            if (!preg_match('/[^a-zA-Z\d]/', $password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
            }

            if (!empty($password_errors)) {
                $message = "Le mot de passe n'est pas sécurisé : " . implode(" ", $password_errors);
            } else {
                // Hashage du mot de passe avant insertion
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insérer l'utilisateur dans la base de données
                $sql = "INSERT INTO tbl_users (user_nom, user_prenom, user_email, user_password, user_active, is_admin) 
                        VALUES (:nom, :prenom, :email, :password, 1, 0)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":nom", $nom);
                $stmt->bindParam(":prenom", $prenom);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":password", $hashedPassword);

                if ($stmt->execute()) {
                    header('Location: login.php?success=Votre compte a été créé avec succès');
                    exit;
                } else {
                    $message = "Erreur lors de la création du compte.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/compte.css">
</head>
<body>

<div class="container">
    <div class="heading">Inscription</div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form action="signup.php" method="POST" class="form">
        <input required class="input" type="text" name="nom" id="nom" placeholder="Nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        <input required class="input" type="text" name="prenom" id="prenom" placeholder="Prénom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
        <input required class="input" type="email" name="email" id="email" placeholder="E-mail" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input required class="input" type="password" name="password" id="password" placeholder="Mot de passe">
        <input required class="input" type="password" name="confirm_password" id="confirm_password" placeholder="Confirmer le mot de passe">
        <input class="login-button" type="submit" value="S'inscrire">
    </form>

    <span class="agreement">Déjà inscrit ? <a href="login.php">Se connecter</a></span>
</div>

</body>
</html>
