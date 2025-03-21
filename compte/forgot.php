<!-- forgot.php -->
<?php
session_start();
require_once '../config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT * FROM tbl_users WHERE user_email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 7200); // 2h

        $stmt_token = $db->prepare("INSERT INTO password_resets (email, token, expires)
                                    VALUES (?, ?, ?)
                                    ON DUPLICATE KEY UPDATE token = VALUES(token), expires = VALUES(expires)");
        $stmt_token->execute([$email, $token, $expires]);

        $reset_link = "http://localhost/app/compte/mdp.php?token=$token";

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
            $mail->addAddress($email);

            $stmt = $db->prepare("SELECT * FROM tbl_users WHERE user_email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $prenom = htmlspecialchars($user['user_prenom']);
            $mail->Body = "
                <div style='font-family:Arial, sans-serif; padding: 20px; background-color:#f9f9f9; border:1px solid #ddd; border-radius:8px; max-width:600px; margin:auto;'>
                <h2 style='color:#1e88e5;'>Oubli App ✔️</h2>
                <p>Bonjour<strong> $prenom</strong>,</p>
                <p>Tu as demandé à réinitialiser ton mot de passe.</p>
                <p>Clique sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>
                <p style='text-align:center;'>
                    <a href='$reset_link' style='display:inline-block; padding:10px 20px; background-color:#1e88e5; color:white; text-decoration:none; border-radius:5px;'>Réinitialiser mon mot de passe</a>
                </p>
                <hr>
                <p style='font-size: 0.9em; color:#555;'>Ce lien est valide pendant <strong>1 heure</strong>.</p>
                <p style='font-size: 0.9em; color:#555;'>⚠️ Ne communique jamais ton mot de passe. Oubli App ne te le demandera jamais par e-mail.</p>
                <p style='font-size: 0.9em; color:#999;'>– L’équipe Oubli App ✔️</p>
            </div>";

            $mail->send();
            $message = "✅ Un lien de réinitialisation a été envoyé.";
        } catch (Exception $e) {
            $message = "❌ Erreur d'envoi : " . $mail->ErrorInfo;
        }
    } else {
        $message = "❌ Email non reconnu.";
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
<body class="p-5">
    <div class="container">
        <h2>Mot de passe oublié</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email :</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer le lien</button>
        </form>
    </div>
</body>
</html>
