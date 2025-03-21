<?php

// Vérifier si la session est déjà active avant de la démarrer
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once '../models/tasks.php';

class taskcontroller {
    private $db_tasks;
    public $tasks;

    public function __construct() {
        $database = new Database();
        $this->db_tasks = $database->getConnection();
        $this->tasks = new Task($this->db_tasks);
    }

    // Méthode pour gérer les actions
    public function handleAction($action, $id, $status) {
        if ($action === 'toggleActif') {
            $this->toggleActif($id);
            header('Location: ../tasks/index.php?status=' . $status); // Conserver le filtre
            exit;
        } elseif ($action === 'delete') {
            $this->delete($id);
            header('Location: ../tasks/index.php?status=' . $status); // Conserver le filtre
            exit;
        } elseif ($action === 'changeStatus') {
            $newStatus = isset($_GET['status']) ? intval($_GET['status']) : null;
            if ($id > 0 && $newStatus !== null) {
                $this->changeStatus($id, $newStatus);
            }
            header('Location: ../tasks/index.php?status=' . $status); // Conserver le filtre
            exit;
        } elseif ($action === 'archive') {
            if ($id > 0) {
                $this->archive($id);
                header('Location: ../tasks/index.php?status=' . $status); // Conserver le filtre
                exit;
            }
        } elseif ($action === 'deleteForever') {
            if ($id > 0) {
                $this->deleteForever($id);
                header('Location: ../tasks/index.php?status=' . $status); // Conserver le filtre
                exit;
            }
        }
        elseif ($action === 'update') {
            if ($id > 0) {
                $this->updateTask($id);
                exit;
            }
        }        
    }

    public function index() {

        if (!isset($_SESSION['user_id'])) {
            header('Location: ../compte/login.php');
            exit;
        }

        $status = isset($_GET['status']) ? intval($_GET['status']) : 1; // Par défaut, afficher les tâches actives
        $user_id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté
        $tasks = $this->tasks->getTasksByStatus($status, $user_id); // Passer les deux arguments
        require_once '../tasks/index.php';
    }  
    
    public function create() {
    
        if ($_POST) {    
            $this->tasks->tasks_name = $_POST['tasks_name'] ?? null;
            $this->tasks->tasks_date = !empty($_POST['tasks_date']) ? $_POST['tasks_date'] : null;
            $this->tasks->tasks_active = isset($_POST['tasks_active']) ? intval($_POST['tasks_active']) : 1;
            $this->tasks->statut_id = 1;
            $this->tasks->user_id = $_SESSION['user_id'];
    
            if ($this->tasks->create()) {
                echo "Tâche créée avec succès.";
                header('Location: ../tasks/index.php');
                exit;
            } else {
                echo "Erreur lors de l'insertion de la tâche.";
                print_r($this->db_tasks->errorInfo());
                exit;
            }
        } else {
            die("Aucune donnée reçue.");
        }
    }

    public function updateTask($id) {
        if ($_POST) {
            $tasks_name = $_POST['tasks_name'] ?? null;
            $tasks_date = $_POST['tasks_date'] ?? null;
    
            $this->tasks->update($id, $tasks_name, $tasks_date);
    
            header('Location: ../tasks/index.php');
            exit;
        }
    }    
    
    public function deleteForever($id) {
        if ($id > 0) {
            $this->tasks->deleteTaskFromDB($id);
        }
    }   

    public function archive($id) {
        $this->tasks->archive($id);
        header('Location: index.php');
        exit;
    }

    public function changeStatus($id, $newStatus) {
        if (in_array($newStatus, [0, 1, 2])) { // Vérifie que le statut est valide
            $this->tasks->updateStatus($id, $newStatus);
        }
        header('Location: index.php');
        exit;
    }
    

    public function toggleActif($id) {
        $task = $this->tasks->getTaskById($id); // Récupérer la tâche
    
        if ($task) {
            if ($task['tasks_active'] == 1) {
                // Désactiver (archiver) la tâche
                $this->tasks->archive($id);
            }
        }
    }

    public function delete($id) {
        // Suppression de la tâche (mettre à jour son statut)
        $this->tasks->complete($id);
    }
}
?>
