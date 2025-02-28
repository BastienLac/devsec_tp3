<?php
session_start();
require 'config.php'; // Connexion à la BDD

$user_id = $_SESSION['user_id'];

// Get task details from URL query parameters
if (isset($_GET['task']) && isset($_GET['id']) && isset($_GET['priority'])) {
    $task = $_GET['task'];
    $id = $_GET['id'];
    $priority = $_GET['priority'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $id = $_POST['id'];  
    
    $sql = "UPDATE task SET text = '$task', priority = '$priority' WHERE id = '$id' AND user_id = '$user_id'";

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
        <!-- Hidden input field for task ID -->
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label for="task">Tâche :</label>
        <input type="text" name="task" required value="<?php echo $task; ?>"><br>

        <label for="priority">Priorité :</label>
        <select name="priority">
            <option value="critical" <?php echo ($priority == 'critical') ? 'selected' : ''; ?>>Critique</option>
            <option value="high" <?php echo ($priority == 'high') ? 'selected' : ''; ?>>Élevée</option>
            <option value="medium" <?php echo ($priority == 'medium') ? 'selected' : ''; ?>>Moyenne</option>
            <option value="low" <?php echo ($priority == 'low') ? 'selected' : ''; ?>>Basse</option>
        </select><br>

        <button type="submit">Modifier la tâche</button>
    </form>
</body>
</html>
