<?php
session_start();
require 'config.php'; // Fichier contenant la connexion à la BDD

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Préparation de la requête
    $result = $conn->query("SELECT id, password FROM users WHERE username =  '$username' AND password = '$password'");

    if($result){
        $user = $result->fetch_assoc();
        // Stocker l'ID de l'utilisateur dans la session après connexion
        $_SESSION['user_id'] = $user['id'];
        header("Location: create_task.php");  
    }
    else {
        echo "Identifiants incorrects. Utilisateur: " . $username;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
        
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
