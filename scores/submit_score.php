<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}

include '../config.php';

$judge_id = $_SESSION['judge_id'];


// Validate POST input
if (!isset($_POST['contestant_id'], $_POST['scores']) || !is_array($_POST['scores'])) {
    die("Invalid input.");
}

$contestant_id = intval($_POST['contestant_id']);
$scores = $_POST['scores'];


// Save each criterion score
foreach ($scores as $criterion_id => $score) {
  $criterion_id = intval($criterion_id);
  $score = floatval($score);

  // Validate score range
  if ($score < 0 || $score > 100) {
    continue; // or handle error
  }

  // Check if a score already exists
  $check = $conn->prepare("SELECT id FROM scores WHERE contestant_id = ? AND judge_id = ? AND criterion_id = ?");
  $check->bind_param("iii", $contestant_id, $judge_id, $criterion_id);
  $check->execute();
  $result = $check->get_result();

  if ($result->num_rows > 0) {
        // Update existing score
        $update = $conn->prepare("UPDATE scores SET score = ? WHERE contestant_id = ? AND judge_id = ? AND criterion_id = ?");
        $update->bind_param("diii", $score, $contestant_id, $judge_id, $criterion_id);
        $update->execute();
    } else {
        // Insert new score
        $insert = $conn->prepare("INSERT INTO scores (contestant_id, judge_id, criterion_id, score) VALUES (?, ?, ?, ?)");
        $insert->bind_param("iiid", $contestant_id, $judge_id, $criterion_id, $score);
        $insert->execute();
    }
}

$redirect = "add.php";
if (isset($_GET['criterion_id'])) {
    $redirect .= "?criterion_id=" . intval($_GET['criterion_id']);
}
header("Location: $redirect");
exit;