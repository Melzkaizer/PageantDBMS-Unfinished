<?php
// score/view.php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

$judge_id = $_SESSION['judge_id'];
$query = "
SELECT c.name AS contestant_name, cr.name AS criterion_name, cr.percentage, s.score
FROM scores s
JOIN contestants c ON s.contestant_id = c.id
JOIN criteria cr ON s.criterion_id = cr.id
WHERE s.judge_id = ?
ORDER BY c.name, cr.name
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $judge_id);
$stmt->execute();
$result = $stmt->get_result();

$score_data = [];
while ($row = $result->fetch_assoc()) {
  $contestant = $row['contestant_name'];
  $criterion = $row['criterion_name'];
  $percentage = $row['percentage'];
  $score = $row['score'];
  $weighted = ($score * $percentage) / 100;

  $score_data[$contestant]['criteria'][] = [
    'criterion' => $criterion,
    'score' => $score,
    'weighted' => $weighted
  ];
  $score_data[$contestant]['total'] = isset($score_data[$contestant]['total']) ? $score_data[$contestant]['total'] + $weighted : $weighted;
}
$stmt->close();



$judge_id = $_SESSION['judge_id'];
$judge_sql = $conn->prepare("SELECT name, email FROM judges WHERE id = ?");
$judge_sql->bind_param("i", $judge_id);
$judge_sql->execute();
$judge_result = $judge_sql->get_result();
$judge = $judge_result->fetch_assoc();


?>
<!DOCTYPE html>
<html>
<head>
  <title>View Scores</title>

<meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../main_styles.css">
  <style>
    * {
      box-sizing: border-box;
    }

    
  </style>
  
</head>
<body>
  <header>
    <h1>Tabulation Management System - Scores</h1>
  </header>

  <?php foreach ($score_data as $contestant => $data): ?>
    <h3><?= htmlspecialchars($contestant) ?></h3>
    <table border="1" cellpadding="8">
      <tr><th>Criterion</th><th>Score</th><th>Weighted</th></tr>
      <?php foreach ($data['criteria'] as $entry): ?>
        <tr>
          <td><?= htmlspecialchars($entry['criterion']) ?></td>
          <td><?= htmlspecialchars($entry['score']) ?></td>
          <td><?= number_format($entry['weighted'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr><td colspan="2"><strong>Total</strong></td><td><strong><?= number_format($data['total'], 2) ?></strong></td></tr>
    </table><br>
  <?php endforeach; ?>
  <a href="add.php">Back to Score Entry</a>
</body>
</html>