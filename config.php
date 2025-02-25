<?php
$host = 'localhost';  // Adresse du serveur
$dbname = 'todolist'; // Nom de la base de données
$user = 'root';       // Nom d'utilisateur (par défaut)
$pass = '';           // Mot de passe (laisser vide pour un serveur local)

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>
