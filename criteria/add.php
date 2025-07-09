<?php
// criteria/add.php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $percentage = floatval($_POST['percentage']);

  if ($name && $percentage >= 0 && $percentage <= 100) {
    $stmt = $conn->prepare("INSERT INTO criteria (name, percentage) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $percentage);
    $stmt->execute();
    $stmt->close();
    $msg = "Criterion added successfully.";
  } else {
    $msg = "Please enter valid inputs.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Criteria</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <h2>Add New Criterion</h2>
  <?php if (isset($msg)) echo "<p class='notice'>$msg</p>"; ?>
  <form method="post">
    Name: <input type="text" name="name" required><br>
    Percentage: <input type="number" name="percentage" step="0.01" min="0" max="100" required><br>
    <button type="submit">Add</button>
  </form>
  <a href="list.php">Back to Criteria List</a>
</body>
</html>
