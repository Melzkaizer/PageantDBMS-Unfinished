<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM judges WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['judge_id'] = $id;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Judge not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Judge Login</title>
    <link rel="stylesheet" href="login_style.css"/>
</head>
<body>
    <div class="login-box">
    <h2>Login Form</h2>
    <form method="POST" action="authenticate.php">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" placeholder="Enter your email" required />
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required />
      </div>
      <div class="form-group remember">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember</label>
      </div>
      <div class="form-group">
        <button type="submit">Login</button>
      </div>
    </form>
    <h4><a href="login_admin.php">Login as Admin?</a></h4>
  </div>
</body>
</html>