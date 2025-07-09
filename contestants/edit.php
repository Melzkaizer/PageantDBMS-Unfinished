<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

if (!isset($_GET['id'])) {
  header("Location: list.php");
  exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT name, photo FROM contestants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $photo);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $new_name = $_POST['name'];
  $new_photo = $photo;

  if (!empty($_FILES['photo']['name'])) {
    $targetDir = "../contestant_photos/";
    $filename = time() . "_" . basename($_FILES["photo"]["name"]);
    $targetFile = $targetDir . $filename;

    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
      move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile);
      $new_photo = $targetFile;
    }
  }

  $stmt = $conn->prepare("UPDATE contestants SET name = ?, photo = ? WHERE id = ?");
  $stmt->bind_param("ssi", $new_name, $new_photo, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: list.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Contestant</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <h2>Edit Contestant</h2>
  <form method="post" enctype="multipart/form-data">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br>
    Current Photo:<br>
    <img src="<?= htmlspecialchars($photo) ?>" width="100"><br>
    New Photo: <input type="file" name="photo" accept="image/*"><br>
    <button type="submit">Update Contestant</button>
  </form>
  <a href="list.php">Back to List</a>
</body>
</html>