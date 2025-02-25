<?php
session_start();

require 'config.php'; // Connexion à la BDD

$user_id = $_SESSION['user_id'];
$id = '';

if (isset($_GET['task'])) {
    $task = $_GET['task'];
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if (isset($_GET['priority'])) {
    $priority = $_GET['priority'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $priority = $_POST['priority'];

    // Ajout de la tâche dans la base de données
    $sql = "UPDATE TABLE task SET text = '$task', priority = '$priority' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Tâche modifiée avec succès.";
        header("Location: create_task.php");
        exit();
    } else {
        echo "Erreur : " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une tâche</title>
</head>

<body>
    <h2>Modifier une tâche</h2>
    <form action="modify_task.php" method="post">
        <label for="task">Tâche :</label>
        <input type="text" name="task" required value="<?php echo $task ?>"><br>

        <label for="priority">Priorité :</label>
        <select name="priority" value="<?php echo $priority ?>">
            <option value="critical">Critique</option>
            <option value="high">Élevée</option>
            <option value="medium">Moyenne</option>
            <option value="low">Basse</option>
        </select><br>

        <button type="submit">Modifier la tâche</button>
    </form>
</body>

</html>