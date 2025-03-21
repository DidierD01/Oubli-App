<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id_user, user_nom, user_prenom, user_email, is_admin FROM tbl_users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Modification d'un utilisateur
    if (isset($_POST['id_user'])) {
        $id_user = $_POST['id_user'];
        $nom = $_POST['user_nom'];
        $prenom = $_POST['user_prenom'];
        $email = $_POST['user_email'];
        $is_admin = (int) $_POST['is_admin'];

        $stmt = $conn->prepare("UPDATE tbl_users SET user_nom = ?, user_prenom = ?, user_email = ?, is_admin = ? WHERE id_user = ?");
        $stmt->execute([$nom, $prenom, $email, $is_admin, $id_user]);

        header("Location: admin.php");
        exit;
    }

    // Suppression d'un utilisateur
    if (isset($_POST['delete_user_id'])) {
        $idToDelete = (int) $_POST['delete_user_id'];

        if ($idToDelete == $_SESSION['user_id']) {
            echo "<div class='alert alert-danger'>❌ Tu ne peux pas supprimer ton propre compte.</div>";
        } else {
            $stmt = $conn->prepare("DELETE FROM tbl_users WHERE id_user = ?");
            $stmt->execute([$idToDelete]);
            header("Location: admin.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Administrateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/compte.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../tasks/oubli_app.png" alt="Logo Oubli App" width="110" height="110" class="d-inline-block align-text-center">
        </a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="setting.php" class="btn btn-info me-2">
                    <i class="fa-solid fa-gear"></i> Paramètres
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Contenu principal -->
<div class="container mt-5" style="max-width: 100%; width: fit-content;">
    <h2>Interface d'administration</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['user_nom']) ?> <?= htmlspecialchars($u['user_prenom']) ?></td>
                <td><?= htmlspecialchars($u['user_email']) ?></td>
                <td>
                    <?php if ($u['is_admin']): ?>
                        <span class="badge bg-success">Admin</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Utilisateur</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editUser<?= $u['id_user'] ?>">Modifier</a></button>
                    <form method="POST" action="admin.php" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                        <input type="hidden" name="delete_user_id" value="<?= $u['id_user'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>

            <!-- Offcanvas pour édition -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editUser<?= $u['id_user'] ?>" aria-labelledby="editUserLabel<?= $u['id_user'] ?>">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="editUserLabel<?= $u['id_user'] ?>">Modifier l'utilisateur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
        </div>
        <div class="offcanvas-body">
            <form action="admin.php" method="POST">
                <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">

                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="user_nom" class="form-control" value="<?= htmlspecialchars($u['user_nom']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="user_prenom" class="form-control" value="<?= htmlspecialchars($u['user_prenom']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="user_email" class="form-control" value="<?= htmlspecialchars($u['user_email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rôle</label>
                    <select name="is_admin" class="form-select">
                        <option value="0" <?= $u['is_admin'] == 0 ? 'selected' : '' ?>>Utilisateur</option>
                        <option value="1" <?= $u['is_admin'] == 1 ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Enregistrer</button>
            </form>
        </div>
    </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<br><br><br>
<?php include('footer.php'); ?>
