<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO judges (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);

  if ($stmt->execute()) {
    $msg = "Judge added successfully.";
  } else {
    $msg = "Error: " . $conn->error;
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Judge</title>
  <link rel="stylesheet" href="../add_style.css">
</head>
<body>
<header>
    <h1>Tabulation Management System</h1>
</header>
<nav>
    <a href="/../index.php">🏠Home</a>
    <div class="dropdown">
      <button class="dropbtn">👤Judges ▼</button>
      <div class="dropdown-content">
        <a href="/../judges/add.php">Add Judge</a>
        <a href="/../judges/list.php">List/Modify Judges</a>
      </div>
    </div>

    <div class="dropdown">
      <button class="dropbtn" >👤Contestants ▼</button>
      <div class="dropdown-content">
        <a href="/../contestants/add.php">Add Contestant</a>
        <a href="/../contestants/list.php">List/Modify Contestant</a>
      </div>
    </div>

    <div class="dropdown">
      <button class="dropbtn">📊Criteria ▼</button>
      <div class="dropdown-content">
        <a href="/../criteria/add.php">Add Criteria</a>
        <a href="/../criteria/list.php">List/Modify Criteria</a>
      </div>
    </div>
    
    <a href="/../scores/add.php">📊Score Input</a>
    <a href="/../scores/view.php">📊View Scores</a>
    <a href="/../reset_password.php">🔐Reset Password</a>
    <a href="/../logout.php">🚪Logout</a>
  </nav>

  <div class="container">
    <form class="form-card" method="POST" action="add_process.php">
      <h2>Add New Judge</h2>
      
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required>
      
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Temporary Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Add Judge</button>

      <a href="list.php">Back to Judge List</a>
    </form>

    
  </div>
  
    <footer>
    &copy; <?php echo date('Y'); ?> Tabulation System
    <br>
    &copy; Single Pa din :(
  </footer>
</body>
</html>