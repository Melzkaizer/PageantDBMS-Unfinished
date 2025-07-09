<?php
// criteria/edit.php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $percentage = floatval($_POST['percentage']);

  if ($name && $percentage >= 0 && $percentage <= 100) {
    $stmt = $conn->prepare("UPDATE criteria SET name = ?, percentage = ? WHERE id = ?");
    $stmt->bind_param("sdi", $name, $percentage, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: list.php");
    exit();
  } else {
    $msg = "Please enter valid inputs.";
  }
} else {
  $stmt = $conn->prepare("SELECT * FROM criteria WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = $result->fetch_assoc();
  $stmt->close();
  if (!$data) {
    die("Criterion not found.");
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Criterion</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <h2>Edit Criterion</h2>
  <?php if (isset($msg)) echo "<p class='notice'>$msg</p>"; ?>
  <form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required><br>
    Percentage: <input type="number" name="percentage" step="0.01" min="0" max="100" value="<?= htmlspecialchars($data['percentage']) ?>" required><br>
    <button type="submit">Update</button>
  </form>
  <a href="list.php">Back to Criteria List</a>
</body>
</html>
