<?php
session_start();

// Connexion à la base de données
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Récupération de l'utilisateur
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM tbl_users WHERE id_user = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable");
}

// Import PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset_password'])) {
        // Générer le token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 7200);

        // Insérer ou mettre à jour le token
        $stmt_token = $conn->prepare("INSERT INTO password_resets (email, token, expires)
                                      VALUES (?, ?, ?)
                                      ON DUPLICATE KEY UPDATE token = VALUES(token), expires = VALUES(expires)");
        $stmt_token->execute([$user['user_email'], $token, $expires]);

        // Lien de réinitialisation
        $reset_link = "http://localhost/app/compte/mdp.php?token=$token";

        // Envoi de l'email
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'oubliapp@gmail.com';
            $mail->Password = 'kzhy vmpr xnzt qqmv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('oubliapp@gmail.com', 'OubliApp');
            $mail->addAddress($user['user_email']);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body = "Bonjour,<br><br>Cliquez ici pour réinitialiser votre mot de passe : <a href='$reset_link'>$reset_link</a><br><br>Ce lien expire dans 2 heures.";

            $mail->send();
            echo "<div class='alert alert-success fade show' role='alert' id='alertBox'>Un email de réinitialisation a été envoyé.</div>";

        } catch (Exception $e) {
            echo "<div class='alert alert-danger fade show' role='alert' id='alertBox'>Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}</div>";
        }
    } else {
        // Traitement modification infos de profil
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];

        $stmt_update = $conn->prepare("UPDATE tbl_users SET user_nom = ?, user_prenom = ?, user_email = ? WHERE id_user = ?");
        $stmt_update->execute([$nom, $prenom, $email, $user_id]);
        header('Location: ../tasks/index.php');
        echo "<div class='alert alert-success fade show' role='alert' id='alertBox''>Profil mis à jour avec succès.</div>";
        exit;
    }

    if (isset($_POST['delete_account'])) {
        // Supprimer l'utilisateur
        $stmt_delete = $conn->prepare("DELETE FROM tbl_users WHERE id_user = ?");
        $stmt_delete->execute([$user_id]);
    
        // Détruire la session
        session_destroy();
    
        // Redirection vers la page de login ou accueil
        header('Location: ../login.php');
        exit;
    }
    
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/compte.css">
</head>
<?php include('nav.php'); ?>
<body>
<div class="container mt-5">
    <h1 class="text-center">Paramètres</h1>
    <form action="setting.php" method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['user_nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['user_prenom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['user_email']) ?>" required>
        </div>
        <!-- Mot de passe non modifiable ici -->
        <div class="mb-3">
            <button type="submit" name="reset_password" class="btn btn-secondary">Modifier le mot de passe</button>
            <input type="text" class="form-control" id="password" name="password" value="********" disabled><br>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer les modifications</button><br><br>
    </form>
    <form action="setting.php" method="POST" onsubmit="return confirm('❗️Es-tu sûr de vouloir supprimer ton compte ? Cette action est irréversible.')">
        <input type="hidden" name="delete_account" value="1">
        <button type="submit" class="btn btn-danger">Supprimer le compte</button>
    </form>
</div>
<br><br><br><br>
<?php include('footer.php'); ?>
