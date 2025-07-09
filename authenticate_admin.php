<?php
session_start();
include 'config.php'; // Make sure this sets up $conn correctly

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        echo "Please fill in all fields.";
        exit;
    }

    // Check DB connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare query using email
    $stmt = $conn->prepare("SELECT admin_id, name, password FROM admin WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if judge exists
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // For security, use password_verify() if passwords are hashed
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['admin_name'] = $user['name'];
            header("Location: index.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
