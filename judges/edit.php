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

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT name, email FROM judges WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $stmt = $conn->prepare("UPDATE judges SET name = ?, email = ? WHERE id = ?");
  $stmt->bind_param("ssi", $name, $email, $id);
  $stmt->execute();
  $stmt->close();
  header("Location: list.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Judge</title>
  
  <link rel="stylesheet" href="/../main_styles.css">
  <style>
     .login-box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 24px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .form-group input[type="text"],
    .form-group input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }

    .form-group input[type="checkbox"] {
      margin-right: 5px;
    }

    .form-group .remember {
      display: flex;
      align-items: center;
    }

    .form-group button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .form-group button:hover {
      background-color: #0056b3;
    }


    .form-container {
  display:flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: linear-gradient(to bottom right, #fff8e1, #ffe0b2);
}

.form-container1 {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.form-card {
  background-color: white;
  padding: 30px 40px;
  border-radius: 10px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
  width: 100%;
  max-width: 400px;
}

.form-card h2 {
  text-align: center;
  margin-bottom: 25px;
  color: #ff6f00;
}

.form-card label {
  display: block;
  margin-bottom: 6px;
  font-weight: bold;
  color: #333;
}

.form-card input {
  width: 100%;
  padding: 10px 12px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

.form-card button {
  width: 100%;
  padding: 12px;
  background-color: #ffa000;
  color: white;
  font-size: 16px;
  font-weight: bold;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.form-card button:hover {
  background-color: #ff8f00;
}
  </style>

</head>
<body>
  <header>
    <h2>Edit Judge</h2>
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


  <form method="post">
    Name: <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>
    <button type="submit">Update Judge</button>
  </form>
  <a href="list.php">Back</a>


  <footer>
    &copy; <?php echo date('Y'); ?> Tabulation System
    <br>
    &copy; Single Pa din :(
  </footer>
</body>
</html>