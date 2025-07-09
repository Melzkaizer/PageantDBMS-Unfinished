<?php
// criteria/list.php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

// Handle delete request
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM criteria WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  header("Location: list.php");
  exit();
}

$result = $conn->query("SELECT * FROM criteria ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Criteria List</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <h2>Criteria List</h2>
  <a href="add.php">Add New Criterion</a>
  <table border="1" cellpadding="10">
    <tr>
      <th>Name</th>
      <th>Percentage</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['percentage']) ?>%</td>
      <td>
        <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
        <a href="list.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this criterion?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <a href="../index.php">Back to Home</a>
</body>
</html>