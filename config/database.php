<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'db_app'; // Change si nécessaire
    private $username = 'root';  // Change si nécessaire
    private $password = ''; // Change si nécessaire
    private $conn;

    // Méthode pour établir la connexion à la base de données
    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn; // ✅ Retourner la connexion si elle existe déjà
        }

        try {
            // Connexion PDO avec encodage UTF-8
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8", 
                $this->username, 
                $this->password, 
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les erreurs PDO
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Récupération des résultats sous forme de tableau associatif
                    PDO::ATTR_PERSISTENT => true // Utiliser une connexion persistante pour améliorer les performances
                ]
            );
        } catch (PDOException $exception) {
            // Stopper le script et afficher une erreur
            die("❌ Erreur de connexion à la base de données : " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
