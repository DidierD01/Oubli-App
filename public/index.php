<?php

// Inclure les fichiers nécessaires
require_once '../config/database.php';
require_once '../controllers/taskcontroller.php';

// Récupérer l'action et l'ID de l'URL (si présents)
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Créer une instance du contrôleur Produit
$taskcontroller = new taskcontroller();

// Rediriger en fonction de l'action (index, create, edit, delete, toggleActif, details)
if ($action == 'create') {
    $taskcontroller->create();
} elseif ($action == 'delete' && $id) {
    $taskcontroller->delete($id);
} elseif ($action == 'toggleActif' && $id) {
    $taskcontroller->toggleActif($id);
} else {
    $taskcontroller->index(); // Afficher la liste des produits par défaut
}
?>