<?php
session_start();
require_once '../config/database.php';

$message = '';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM tbl_users WHERE user_email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nom'] = $user['user_nom'];
            $_SESSION['user_prenom'] = $user['user_prenom'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['is_admin'] = $user['is_admin'];

            echo "Connexion réussie, User ID = " . $_SESSION['user_id'];

            if ($user['is_admin'] == 1) {
                header("Location: admin.php"); // ✅ vers la page admin
            } else {
                header("Location: ../tasks/index.php");  // ✅ utilisateur classique
            }
            exit;
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/compte.css">
</head>
<body>

<div class="container">
    <div class="heading">Connexion</div>
    
    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="form">
        <input required class="input" type="email" name="email" id="email" placeholder="E-mail">
        <input required class="input" type="password" name="password" id="password" placeholder="Mot de passe">
        <span class="forgot-password"><a href="forgot.php">Mot de passe oublié ?</a></span>
        <input class="login-button" type="submit" value="Se connecter">
    </form>

    <span class="agreement">Pas encore de compte ? <a href="signup.php">S'inscrire</a></span>
</div>

</body>
</html>
