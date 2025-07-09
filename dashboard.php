<?php
session_start();

include 'config.php';

if (!isset($_SESSION['judge_id'])) {
  header("Location: login.php");
  exit();
}

// Fetch judge info
$judge_id = $_SESSION['judge_id'];
$judge_sql = $conn->prepare("SELECT name, email FROM judges WHERE id = ?");
$judge_sql->bind_param("i", $judge_id);
$judge_sql->execute();
$judge_result = $judge_sql->get_result();
$judge = $judge_result->fetch_assoc();

// Fetch contestants
$contestants_result = $conn->query("SELECT name, photo, id FROM contestants");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tabulation Management System</title>
  <link rel="stylesheet" href="main_styles.css">
  <style>
    * {
      box-sizing: border-box;
    }

    
  </style>
</head>
<body>
  <header>
    <h1>Tabulation Management System</h1>
  </header>

  <nav>
    <a href="index.php" class="active">🏠Home</a>

    <div class="dropdown">
      <button class="dropbtn">👤Contestants ▼</button>
      <div class="dropdown-content">
        <a href="contestants/add.php">Add Contestant</a>
        <a href="contestants/list.php">List/Modify Contestant</a>
      </div>
    </div>
    
    <a href="scores/add.php">📊Score Input</a>
    <a href="scores/view.php">📊View Scores</a>
    <a href="reset_password.php">🔐Reset Password</a>
    <a href="logout.php">🚪Logout</a>
  </nav>

  <div class="container">
    <div class="sidebar">
      <h3>User Info</h3>
      <p><strong>Logged in as:</strong></p>
      <p><?php echo htmlspecialchars($_SESSION['judge_name'] ?? 'Judge'); ?></p>
      <hr>
      <p><a href="logout.php">Log out</a></p>
    </div>

    <div class="main">
      <h2>Welcome</h2>
      <p>Welcome to the Tabulation Management System. Created by a lonely single guy. If you could help him find a GF, he will give you the source code of this for free. Use the menu to manage judges, contestants, criteria, and scores etc.</p>

      <h2>Contestants</h2>
      <?php while ($row = $contestants_result->fetch_assoc()): ?>
        <div class="contestant">
          <img src="<?= htmlspecialchars($row['photo']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
          <span><?= htmlspecialchars($row['name']) ?></span>
        </div>
      <?php endwhile; ?>
      <h2>Judges</h2>
      <?php
        $judges = $conn->query("SELECT * FROM judges");
        while ($row = $judges->fetch_assoc()) {
          echo "<div class='card'>
                  <p><strong>" . htmlspecialchars($row['name']) . "</strong></p>
                  <p class='email'>" . htmlspecialchars($row['email']) . "</p>
                </div>";
        }
      ?>
    </div>
  </div>

  <footer>
    &copy; <?php echo date('Y'); ?> Tabulation System
    <br>
    &copy; Single Pa din :(
  </footer>
</body>
</html>
