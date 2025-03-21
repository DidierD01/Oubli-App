<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="oubli_app.png" alt="Logo Oubli App" width="110" height="110" class="d-inline-block align-text-center">
        </a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Boutons pour l'utilisateur connecté -->
                <a href="../compte/setting.php" class="btn btn-info me-2">
                    <i class="fa-solid fa-gear"></i> Paramètres
                </a>
                <a href="../compte/logout.php" class="btn btn-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
                </a>
            <?php else: ?>
                <!-- Bouton pour l'utilisateur non connecté -->
                <a href="../compte/login.php" class="btn btn-primary">
                    Se Connecter
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>