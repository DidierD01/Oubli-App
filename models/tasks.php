<?php

// Vérifier si la session est déjà active avant de la démarrer
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Task {
    private $conn;
    private $tbl_tasks = "tbl_tasks"; // Vérifiez que le nom de la table est correct

    public $id_tasks;
    public $tasks_name;
    public $statut_id;
    public $user_id;

    public function __construct($db_tasks) {
        $this->conn = $db_tasks;
    }

    public function create() {
        $query = "INSERT INTO tbl_tasks (tasks_name, tasks_date, tasks_active, user_id, statut_id)
                  VALUES (:tasks_name, :tasks_date, :tasks_active, :user_id, :statut_id)";
    
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(":tasks_name", $this->tasks_name);
        $stmt->bindParam(":tasks_date", $this->tasks_date);
        $stmt->bindParam(":tasks_active", $this->tasks_active, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
        $stmt->bindParam(":statut_id", $this->statut_id, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erreur SQL : ";
            print_r($stmt->errorInfo()); // Afficher l'erreur SQL
            return false;
        }
    }       

    public function archive($id) {
        $query = "UPDATE " . $this->tbl_tasks . " SET statut_id = 2, tasks_active = 0 WHERE id_tasks = :id_tasks";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tasks", $id, PDO::PARAM_INT);
        return $stmt->execute(); // Vérifiez si l'exécution réussit
    }

    public function complete($id) {
        $query = "UPDATE " . $this->tbl_tasks . " SET statut_id = 0 WHERE id_tasks = :id_tasks";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tasks", $id, PDO::PARAM_INT);
        return $stmt->execute(); // Vérifiez si l'exécution réussit
    }

    public function update($id, $name, $date) {
        $query = "UPDATE " . $this->tbl_tasks . " 
                  SET tasks_name = :name, tasks_date = :date 
                  WHERE id_tasks = :id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }    

    public function delete() {
        $query = "DELETE FROM " . $this->tbl_tasks . " WHERE id_tasks = :id_tasks";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tasks", $this->id_tasks, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteTaskFromDB($id) {
        $sql = "DELETE FROM " . $this->tbl_tasks . " WHERE id_tasks = ?"; // Utiliser $this->tbl_tasks
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]); // S'assurer que la méthode renvoie le résultat de l'exécution
    }    

    public function updateStatus($id, $newStatus) {
        $query = "UPDATE " . $this->tbl_tasks . " SET statut_id = :newStatus WHERE id_tasks = :id_tasks";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":newStatus", $newStatus, PDO::PARAM_INT);
        $stmt->bindParam(":id_tasks", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }    

    // Méthode pour obtenir une tâche par son ID (si nécessaire)
    public function getTaskById($id) {
        $query = "SELECT * FROM " . $this->tbl_tasks . " WHERE id_tasks = :id_tasks";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tasks", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllTasks() {
        $query = "SELECT * FROM tbl_tasks";
        $stmt = $this->conn->prepare($query); // Utilise la connexion pour préparer la requête
        $stmt->execute(); // Exécuter la requête
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourner les résultats
    }

    public function getTasksByStatus($status, $user_id) {
        $query = "SELECT * FROM tbl_tasks WHERE statut_id = :status AND user_id = :user_id ORDER BY tasks_date IS NULL, tasks_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }        

    public function toggleActif($id) {
        $query = "SELECT tasks_active FROM " . $this->tbl_tasks . " WHERE id_tasks = :id_tasks";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_tasks", $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            $newActif = $task['tasks_active'] == 1 ? 0 : 1;

            $updateQuery = "UPDATE " . $this->tbl_tasks . " SET tasks_active = :tasks_active WHERE id_tasks = :id_tasks";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":tasks_active", $newActif, PDO::PARAM_INT);
            $updateStmt->bindParam(":id_tasks", $id, PDO::PARAM_INT);

            return $updateStmt->execute();
        } else {
            return false;
        }
    }
}
?>
