<?php
session_start();

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $token = $_POST['token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        

        // Vérifier si les mots de passe correspondent
        if ($new_password !== $confirm_password) {
            die("❌ Les mots de passe ne correspondent pas.");
        }

        // Vérifier si le token existe et est valide
        $sql = "SELECT email FROM password_resets WHERE token = ? AND expires > NOW()";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            die("Erreur de préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            die("Erreur lors de l'exécution de la requête : " . $stmt->error);
        }

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];

            // ✅ Vérifier la sécurité du mot de passe
            $password_errors = [];

            if (strlen($new_password) < 8) {
                $password_errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
            }
            if (!preg_match('/\\d/', $new_password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins un chiffre.";
            }
            if (!preg_match('/[A-Z]/', $new_password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins une lettre majuscule.";
            }
            if (!preg_match('/[a-z]/', $new_password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins une lettre minuscule.";
            }
            if (!preg_match('/[^a-zA-Z\\d]/', $new_password)) {
                $password_errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
            }

            if (!empty($password_errors)) {
                die("Le mot de passe n'est pas sécurisé : " . implode(" ", $password_errors));
            }

            // ✅ Hacher le nouveau mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // ✅ Mettre à jour le mot de passe dans la base de données
            $sql_update = "UPDATE tbl_users SET user_password = ? WHERE user_email = ?";
            $stmt_update = $conn->prepare($sql_update);
            
            if (!$stmt_update) {
                die("Erreur de préparation de la requête d'update : " . $conn->error);
            }

            $stmt_update->bind_param("ss", $hashed_password, $email);
            
            if ($stmt_update->execute()) {
                echo "✅ Mot de passe mis à jour avec succès !";

                // ✅ Supprimer le token après utilisation
                $sql_delete = "DELETE FROM password_resets WHERE email = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("s", $email);
                $stmt_delete->execute();
            } else {
                die("❌ Erreur lors de la mise à jour du mot de passe.");
            }
        } else {
            die("❌ Lien de réinitialisation invalide ou expiré.");
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification du Mot de Passe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/compte.css">
</head>
<?php include('nav.php'); ?>
<br><br>
<div class="container">
    <div class="form-reset">
        <h2 class="mb-4 text-center">Réinitialiser votre mot de passe</h2>
        <form method="POST" action="mdp.php">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? $_POST['token'] ?? '') ?>">

            <div class="mb-3">
                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Changer le mot de passe</button>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>
