<?php
session_start();
include 'config.php';

if (!isset($_SESSION['judge_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judge_id = $_SESSION['judge_id'];
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    $stmt = $conn->prepare("SELECT password FROM judges WHERE id = ?");
    $stmt->bind_param("i", $judge_id);
    $stmt->execute();
    $stmt->bind_result($hashed);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($current, $hashed)) {
        $new_hashed = password_hash($new, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE judges SET password = ? WHERE id = ?");
        $update->bind_param("si", $new_hashed, $judge_id);
        if ($update->execute()) {
            $message = "Password updated successfully.";
        } else {
            $message = "Error updating password.";
        }
        $update->close();
    } else {
        $message = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h2>Reset Password</h2>
  <?php if (isset($message)) echo "<p class='notice'>$message</p>"; ?>
  <form method="post">
    Current Password: <input type="password" name="current_password" required><br>
    New Password: <input type="password" name="new_password" required><br>
    <button type="submit">Reset Password</button>
  </form>
  <a href="index.php">Back to Dashboard</a>
</body>
</html>