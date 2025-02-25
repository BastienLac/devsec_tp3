<?php
session_start();

require 'config.php'; // Connexion à la BDD

$user_id = $_SESSION['user_id'];
$sql = "SELECT task.id, task.text, task.priority 
        FROM task 
        WHERE task.user_id = '$user_id'";

$result = $conn->query($sql);
$tasks = [];

if ($result->num_rows > 0) {
    // Fetch all tasks for the logged-in user
    while($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    
    // Échappement de la tâche
    $task = mysqli_real_escape_string($conn, $task);
    
    // Ajout de la tâche dans la base de données
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO tasks (user_id, task) VALUES ('$user_id', '$task')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Tâche ajoutée avec succès.";
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
    <title>Créer une tâche</title>
</head>
<body>
    <h2>Créer une nouvelle tâche</h2>
    <form action="create_task.php" method="post">
        <label for="task">Nouvelle tâche :</label>
        <input type="text" name="task" required><br>
        <button type="submit">Ajouter la tâche</button>
    </form>

    <?php if (empty($tasks)): ?>
        <p>Pas de tâches</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Priorité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['text']); ?></td>
                        <td><?php echo htmlspecialchars($task['priority']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
