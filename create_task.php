<?php
session_start();
require 'config.php'; // Connexion à la BDD

$user_id = $_SESSION['user_id'];

// Ensure the user is logged in
if (!isset($user_id)) {
    header("Location: login.php");
    exit();
}

// Prepared statement to fetch tasks
$stmt = $conn->prepare("SELECT task.id, task.text, task.priority FROM task WHERE task.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

if (isset($_GET['delete_id'])) {
    $task_id = $_GET['delete_id'];

    // Prevent SQL Injection with prepared statement
    $stmt = $conn->prepare("DELETE FROM task WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    if ($stmt->execute()) {
        echo "Tâche supprimée avec succès.";
        header("Location: create_task.php");
        exit();
    } else {
        echo "Erreur lors de la suppression : " . $stmt->error;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $search_text = '';
    $priority_filter = '';
    if (isset($_GET['search_text'])) {
        $search_text = $_GET['search_text'];
    }
    if (isset($_GET['priority_filter'])) {
        $priority_filter = $_GET['priority_filter'];
    }

    // Prepared statement for filtering tasks
    $sql = "SELECT task.id, task.text, task.priority FROM task WHERE task.user_id = ?";
    if ($search_text) {
        $sql .= " AND task.text LIKE ?";
    }
    if ($priority_filter && $priority_filter != '--') {
        $sql .= " AND task.priority = ?";
    }

    $stmt = $conn->prepare($sql);
    if ($search_text && $priority_filter && $priority_filter != '--') {
        $search_text = "%$search_text%";
        $stmt->bind_param("iss", $user_id, $search_text, $priority_filter);
    } elseif ($search_text) {
        $search_text = "%$search_text%";
        $stmt->bind_param("is", $user_id, $search_text);
    } elseif ($priority_filter && $priority_filter != '--') {
        $stmt->bind_param("is", $user_id, $priority_filter);
    } else {
        $stmt->bind_param("i", $user_id);
    }

    $stmt->execute();
    $filtered_tasks = $stmt->get_result();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST['task'];
    $priority = $_POST['priority'];

    // Prepared statement to insert a task
    $stmt = $conn->prepare("INSERT INTO task (text, priority, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $task, $priority, $user_id);

    if ($stmt->execute()) {
        echo "Tâche ajoutée avec succès.";
    } else {
        echo "Erreur : " . $stmt->error;
    }
    $stmt->close();
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

    <h2>Filtrer les tâches</h2>
    <form action="create_task.php" method="get">
        <label for="search_text">Rechercher (par description) :</label>
        <input type="text" name="search_text" value="<?php echo htmlspecialchars($search_text); ?>"><br>

        <label for="priority_filter">Filtrer par priorité :</label>
        <select name="priority_filter">
            <option value="--">--</option>
            <option value="critical" <?php echo ($priority_filter == 'critical') ? 'selected' : ''; ?>>Critique</option>
            <option value="high" <?php echo ($priority_filter == 'high') ? 'selected' : ''; ?>>Haute</option>
            <option value="medium" <?php echo ($priority_filter == 'medium') ? 'selected' : ''; ?>>Moyenne</option>
            <option value="low" <?php echo ($priority_filter == 'low') ? 'selected' : ''; ?>>Basse</option>
        </select><br>

        <button type="submit">Filtrer</button>
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

    <h2>Tâches filtrées</h2>

    <?php if (empty($filtered_tasks)): ?>
        <p>Aucune tâche ne correspond à vos critères.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Priorité</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = $filtered_tasks->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['text']); ?></td>
                        <td><?php echo htmlspecialchars($task['priority']); ?></td>
                        <td>
                            <a href="create_task.php?delete_id=<?php echo $task['id']; ?>"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
