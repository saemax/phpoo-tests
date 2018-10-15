<?php
    session_start();

    class PDOConnection {
        protected $pdo, $server, $username, $password, $database;

        public function __construct($server, $username, $password, $database) {
            $this->server = $server;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;

            $this->connectBDD();
        }

        protected function connectBDD() {
            try {
                $this->pdo = new PDO('mysql:host='.$this->server.';dbname='.$this->database, $this->username, $this->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            }
            catch (PDOException $e) {
                echo 'Connexion échouée : ' . $e->getMessage();
            }
        }

        public function DB() {
            return $this->pdo;
        }

        public function __sleep() {
            return ['server', 'username', 'password', 'database'];
        }

        public function __wakeup() {
            $this->connectBDD();
        }
    }

    if (!isset($_SESSION['PDOConnection'])) { // A appeler une fois dans votre site.
        $PDOConnection = new PDOConnection('localhost', 'root', '', 'sae');
        $_SESSION['PDOConnection'] = $PDOConnection; // A la fin du script, serialize() est automatiquement appelé sur les sessions.
        echo 'Rechargez la page.';
    }
    else { // A appeler quand vous voulez utiliser votre base de donnée dans votre page.
        $PDOConnection = $_SESSION['PDOConnection']->DB(); // Au début du script, unserialize() est automatiquement appelé sur les sessions.

        $q = $PDOConnection->prepare("SELECT * FROM articles ORDER BY date DESC");
        $q->execute();

        $data = $q->fetchAll(PDO::FETCH_ASSOC);

        echo '<pre>';
            print_r($data);
        echo '</pre>';
    }
?>