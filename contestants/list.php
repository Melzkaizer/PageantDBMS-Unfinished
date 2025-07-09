<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

$result = $conn->query("SELECT id, name, photo FROM contestants");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Contestant List</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <h2>Contestants</h2>
  <a href="add.php">Add New Contestant</a>
  <table border="1" cellpadding="5" cellspacing="0">
    <><th>Photo</th><th>Name</th> <th>Actions</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><img src="<?= htmlspecialchars($row['photo']) ?>" alt="Photo" width="80"></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td>
        <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this contestant?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <a href="../index.php">Back to Dashboard</a>
</body>
</html>