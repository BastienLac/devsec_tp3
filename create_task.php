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

if (isset($_GET['delete_id'])) {
    $task_id = $_GET['delete_id'];

    $sql = "DELETE FROM task WHERE id = '$task_id' AND user_id = '$user_id'";

    if ($conn->query($delete_sql) === TRUE) {
        echo "Tâche supprimée avec succès.";
        header("Location: create_task.php");
        exit();
    } else {
        echo "Erreur lors de la suppression : " . $conn->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    
    // Ajout de la tâche dans la base de données
    $sql = "INSERT INTO task (text, priority, user_id) VALUES ('$task', '$priority', '$user_id')";
    
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

        <label for="priority">Priorité :</label>
        <select name="priority" required>
            <option value="critical">Critique</option>
            <option value="high">Élevée</option>
            <option value="medium">Moyenne</option>
            <option value="low">Basse</option>
        </select><br>

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
                        <td>
                            <!-- Delete button with a GET parameter for task ID -->
                            <a href="create_task.php?delete_id=<?php echo $task['id']; ?>" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
