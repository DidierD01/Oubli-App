<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../tasks/oubli_app.png" alt="Logo Oubli App" width="110" height="110" class="d-inline-block align-text-center">
        </a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../tasks/index.php" class="btn btn-secondary">
                    <i class="fa-solid fa-right-from-bracket"></i> Retour à l'accueil
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>