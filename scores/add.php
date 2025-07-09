<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

$judge_id = $_SESSION['judge_id'];

// Get contestants and criteria
$contestants_result = $conn->query("SELECT * FROM contestants ORDER BY name ASC");
$criteria_result = $conn->query("SELECT * FROM criteria ORDER BY name ASC");

$contestants = $contestants_result->fetch_all(MYSQLI_ASSOC);
$criteria = $criteria_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Score Contestants</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    .container {
      text-align: center;
      max-width: 600px;
      margin: 40px auto;
    }
    .contestant {
      display: none;
    }
    .contestant.active {
      display: block;
    }
    .contestant img {
      width: 80%;
      max-height: 450px;
      object-fit: cover;
      border-radius: 12px;
      margin-bottom: 15px;
    }
    .nav-buttons {
      display: flex;
      justify-content: space-between;
      margin: 20px 0;
    }
    .score-form input[type="number"] {
      width: 80px;
      padding: 5px;
    }
    .score-form label {
      display: block;
      margin: 8px 0;
    }
    button {
      padding: 10px 20px;
      font-size: 16px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Judge Score Entry</h2>
  <div class="nav-buttons">
    <button onclick="prevContestant()">← Previous</button>
    <button onclick="nextContestant()">Next →</button>
  </div>

  <?php foreach ($contestants as $index => $con): ?>
    <div class="contestant <?= $index === 0 ? 'active' : '' ?>" id="contestant-<?= $index ?>">
      <h3><?= htmlspecialchars($con['name']) ?></h3>
      <img src="../Upload/<?= htmlspecialchars($con['photo']) ?>" alt="<?= htmlspecialchars($con['name']) ?>">

      <form class="score-form" action="submit_score.php" method="post">
        <input type="hidden" name="contestant_id" value="<?= $con['id'] ?>">
        <?php foreach ($criteria as $c): ?>
          <label>
            <?= htmlspecialchars($c['name']) ?>:
            <input type="number" name="scores[<?= $c['id'] ?>]" min="0" max="100" required>
          </label>
        <?php endforeach; ?>
        <br>
        <button type="submit">Submit Score</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>

<script>
  let current = 0;
  const contestants = document.querySelectorAll('.contestant');

  function show(index) {
    contestants.forEach((el, i) => {
      el.classList.remove('active');
      if (i === index) el.classList.add('active');
    });
  }

  function nextContestant() {
    if (current < contestants.length - 1) {
      current++;
      show(current);
    }
  }

  function prevContestant() {
    if (current > 0) {
      current--;
      show(current);
    }
  }

  show(current);
</script>

<a href="../index.php">Back to Home</a>
<br><br>
<a href="view.php">View Scores</a>
<br><br>
<a href="admin_view.php">View Scores as Admin</a>


</body>
</html>
