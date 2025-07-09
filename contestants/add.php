<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];

  $targetDir = "../contestant_photos/";
  $imageName = basename($_FILES["photo"]["name"]);
  $targetFile = $targetDir . time() . "_" . $imageName;
  $uploadOk = 1;
  $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

  // Check image file
  $check = getimagesize($_FILES["photo"]["tmp_name"]);
  if ($check !== false && in_array($imageFileType, ["jpg", "jpeg", "png"])) {
    move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile);
    $stmt = $conn->prepare("INSERT INTO contestants (name, photo) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $targetFile);
    $stmt->execute();
    $stmt->close();
    $msg = "Contestant added!";
  } else {
    $msg = "Invalid image file.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Contestant</title>
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <h2>Add Contestant</h2>
  <?php if (isset($msg)) echo "<p class='notice'>$msg</p>"; ?>
  <form method="post" enctype="multipart/form-data">
    Name: <input type="text" name="name" required><br>
    Photo: <input type="file" name="photo" accept="image/*" required><br>
    <button type="submit">Add Contestant</button>
  </form>
  <a href="list.php">Back to Contestant List</a>
</body>
</html>