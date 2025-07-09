<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

$result = $conn->query("SELECT id, name, email FROM judges");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Judges List</title>
  <link rel="stylesheet" href="../main_styles.css">
  <style>
    * {
      box-sizing: border-box;
    }

    .table-container {
  display: flex;
  justify-content: center;
  margin-top: 40px;
}

.styled-table {
  border-collapse: separate;
  border-spacing: 0;
  border: double 4px #ffa500;
  background-color: #fff;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

.styled-table th,
.styled-table td {
  border: 2px double #ffa500;
  padding: 10px 20px;
  text-align: left;
}

.styled-table th {
  background-color: #ffcc33;
  color: #000;
  font-weight: bold;
}

.styled-table td {
  background-color: #fff8e1;
}

.action-links a {
  color: #0066cc;
  text-decoration: none;
  margin: 0 5px;
}

.action-links a:hover {
  text-decoration: underline;
}

.action-button {
  padding: 4px 4px;
  font-size: 16px;
  border: solid;
  border-color: #0066cc;
  border-radius: 6px;
  margin: 0 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration: none;
  display: inline-block;
}




  </style>

</head>
<body>
  <header>
    <h1>JUDGES</h1>
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
      <button class="dropbtn">👤Contestants ▼</button>
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
    <div class="sidebar">
      <h3>User Info</h3>
      <p><strong>Logged in as:</strong></p>
      <p><?php echo htmlspecialchars($_SESSION['judge_name'] ?? 'Judge'); ?></p>
      <hr>
      <p><a href="logout.php">Log out</a></p>
    </div>


<div class="main">
      <h2>Judges</h2>
      <div class="table-container">
        <table class="styled-table">
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="action-button">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this judge?')" class="action-button" style="background-color: #e74c3c;">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
      <br>
      <a href="add.php" class="action-button">Add New Judge</a>
      <a href="/../index.php" class="action-button">Back to Dashboard</a>
    </div>
  </div>

  <footer>
    &copy; <?php echo date('Y'); ?> Tabulation System
    <br>
    &copy; Single Pa din :(
  </footer>
</body>
</html>