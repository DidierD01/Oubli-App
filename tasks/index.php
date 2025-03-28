<?php

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupère l'URL demandée
$request = $_SERVER['REQUEST_URI'];

// Supprime les paramètres de requête (tout ce qui suit "?")
$request = strtok($request, '?');

// Redirige en fonction de l'URL
switch ($request) {
    case '/':
    case '/index.php':
        // Affiche la page d'accueil
        echo "Bienvenue sur la page d'accueil !";
        break;
    default:
        // Page non trouvée
        http_response_code(404);
        echo "Page non trouvée :(";
        break;
}

require_once __DIR__ . '/controllers/taskcontroller.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $user_logged_in = false;
} else {
    $user_logged_in = true;
    $user_id = $_SESSION['user_id'];
}

// Instancier le contrôleur
$taskcontroller = new taskcontroller();

// Gérer les actions (archive, delete, etc.)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $status = isset($_GET['status']) ? intval($_GET['status']) : 1; // Récupérer le statut actuel

    // Gérer l'action
    $taskcontroller->handleAction($action, $id, $status);
}

// Si l'utilisateur est connecté, charger le contrôleur et récupérer ses tâches
if ($user_logged_in) {
    // Récupérer le statut de filtrage (1 = actives, 2 = archivées, 0 = terminées)
    $status = isset($_GET['status']) ? intval($_GET['status']) : 1;

    // Récupérer les tâches filtrées par statut
    $tasks = $taskcontroller->tasks->getTasksByStatus($status, $user_id);
}

// Inclure les fichiers du design
$page = 'Accueil';
$titre = "Oubli App";
include('head.php');
include('nav.php');
?>

<div class="container mt-5">
    <?php if (!$user_logged_in): ?>
        <!-- Message si l'utilisateur n'est pas connecté -->
        <div class="alert alert-warning text-center" role="alert">
            <h4>Veuillez vous connecter pour voir vos tâches.</h4>
            <a href="/../compte/login.php" class="btn btn-primary mt-3">Se connecter</a>
        </div>
    <?php else: ?>
        <h1 class="text-center my-4">Mes Tâches</h1>

        <!-- Barre de filtrage -->
        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group" role="group" aria-label="Filtrer les tâches">
                <a href="index.php?status=1" class="btn btn-outline-primary <?= !isset($_GET['status']) || $_GET['status'] == 1 ? 'active' : '' ?>">
                    Tâches Actives
                </a>
                <a href="index.php?status=2" class="btn btn-outline-warning <?= isset($_GET['status']) && $_GET['status'] == 2 ? 'active' : '' ?>">
                    Tâches Archivées
                </a>
                <a href="index.php?status=0" class="btn btn-outline-danger <?= isset($_GET['status']) && $_GET['status'] == 0 ? 'active' : '' ?>">
                    Tâches Terminées
                </a>
            </div>
        </div>

        <!-- Bouton Ajouter une Tâche -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">
                <i class="fa-solid fa-plus"></i> Ajouter une tâche
            </button>
        </div>

        <!-- Formulaire d'ajout de tâche -->
        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasBottomLabel">Ajouter une Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body small">
                <form action="/../public/index.php?action=create" method="POST">
                    <div class="mb-3">
                        <label for="taskName" class="form-label">Nom de la tâche</label>
                        <input type="text" class="form-control" id="taskName" name="tasks_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDate" class="form-label">Date (facultatif)</label>
                        <input type="date" class="form-control" id="taskDate" name="tasks_date">
                    </div>
                    <button type="submit" class="btn btn-primary">Créer la tâche</button>
                </form>
            </div>
        </div>
        <!-- Section des Tâches -->
        <div class="tasks-category" data-status="<?= $status ?>">
            <h2 class="text-center my-4">
                <?php
                switch ($status) {
                    case 1:
                        echo "Tâches Actives";
                        break;
                    case 2:
                        echo "Tâches Archivées";
                        break;
                    case 0:
                        echo "Tâches Terminées";
                        break;
                    default:
                        echo "Toutes les Tâches";
                }
                ?>
            </h2>
            <table class="table">
            <?php foreach ($tasks as $task): ?>
                <div class="offcanvas offcanvas-bottom" tabindex="-1" id="editTask<?= $task['id_tasks'] ?>" aria-labelledby="editTaskLabel<?= $task['id_tasks'] ?>">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="editTaskLabel<?= $task['id_tasks'] ?>">Modifier la Tâche</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer"></button>
                    </div>
                    <div class="offcanvas-body small">
                        <form action="/../public/index.php?action=update&id=<?= $task['id_tasks'] ?>" method="POST">
                            <div class="mb-3">
                                <label for="tasks_name_<?= $task['id_tasks'] ?>" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="tasks_name_<?= $task['id_tasks'] ?>" name="tasks_name" value="<?= htmlspecialchars($task['tasks_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="tasks_date_<?= $task['id_tasks'] ?>" class="form-label">Date</label>
                                <input type="date" class="form-control" id="tasks_date_<?= $task['id_tasks'] ?>" name="tasks_date" value="<?= $task['tasks_date'] ?>">
                            </div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
                <thead class="table-primary">
                    <tr>
                        <th style="width: 25%;">Tâches</th>
                        <th style="width: 25%;">Date</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tasks)): ?>
                        <?php foreach ($tasks as $tasks): ?>
                            <tr>
                                <td><?= htmlspecialchars($tasks['tasks_name']) ?></td>
                                <td><?= $tasks['tasks_date'] ? htmlspecialchars($tasks['tasks_date']) : '/' ?></td>
                                <td class="text-center">
                                    <?php
                                    switch ($tasks['statut_id']) {
                                        case 1:
                                            echo '<span class="badge bg-success">Actif</span>';
                                            break;
                                        case 2:
                                            echo '<span class="badge bg-warning">Inactif</span>';
                                            break;
                                        case 0:
                                            echo '<span class="badge bg-danger">Terminée</span>';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($tasks['statut_id'] == 1): ?>
                                        <a href="index.php?action=archive&id=<?= $tasks['id_tasks'] ?>&status=<?= $status ?>" class="btn btn-warning btn-sm">Archiver</a>
                                        <a href="index.php?action=delete&id=<?= $tasks['id_tasks'] ?>&status=<?= $status ?>" class="btn btn-danger btn-sm">Terminer</a>
                                        <button class="btn btn-light btn-sm" style="border: 1px solid black;" data-bs-toggle="offcanvas" data-bs-target="#editTask<?= $tasks['id_tasks'] ?>">Modifier</button>
                                    <?php elseif ($tasks['statut_id'] == 2): ?>
                                        <a href="index.php?action=changeStatus&id=<?= $tasks['id_tasks'] ?>&status=1" class="btn btn-success btn-sm">Réactiver</a>
                                        <a href="index.php?action=delete&id=<?= $tasks['id_tasks'] ?>&status=<?= $status ?>" class="btn btn-danger btn-sm">Supprimer</a>
                                    <?php elseif ($tasks['statut_id'] == 0): ?>
                                        <a href="index.php?action=changeStatus&id=<?= $tasks['id_tasks'] ?>&status=1" class="btn btn-success btn-sm">Recommencer</a>
                                        <a href="index.php?action=archive&id=<?= $tasks['id_tasks'] ?>&status=<?= $status ?>" class="btn btn-warning btn-sm">Archiver</a>
                                        <a href="index.php?action=deleteForever&id=<?= $tasks['id_tasks'] ?>&status=<?= $status ?>" class="btn btn-danger btn-sm">Supprimer</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucune tâche trouvée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
