<?php
session_start();
if (!isset($_SESSION['judge_id'])) {
  header("Location: ../login.php");
  exit();
}
include '../config.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  // Get the photo path first
  $stmt = $conn->prepare("SELECT photo FROM contestants WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($photo);
  $stmt->fetch();
  $stmt->close();

  // Delete the contestant
  $stmt = $conn->prepare("DELETE FROM contestants WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();

  // Delete the photo file if it exists
  if (file_exists($photo)) {
    unlink($photo);
  }
}

header("Location: list.php");
exit();