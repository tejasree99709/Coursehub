<?php
session_start();

// Hardcoded admin credentials
$admin_username = "admin";
$admin_email = "admin@gmail.com";
$admin_password = "admin@123"; // Replace with a strong password

// Database connection
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password
$dbname = "coursehub"; // Your database name
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginUsername = trim($_POST['username']);
    $loginPassword = trim($_POST['password']);
    $role = trim($_POST['role']); // Retrieve role value from the button

    if ($role === "admin") {
        // Admin login logic
        if (
            ($loginUsername === $admin_username || $loginUsername === $admin_email) &&
            $loginPassword === $admin_password
        ) {
            // Store admin session variables
            $_SESSION['role'] = "admin";
            $_SESSION['username'] = $admin_username;
            $_SESSION['email'] = $admin_email;

            // Redirect to admin dashboard
            header("Location: admin.html");
            exit();
        } else {
            echo "<script>alert('Invalid Admin credentials!'); window.location.href = 'login.html';</script>";
        }
    } elseif ($role === "student") {
        // Student login logic
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $loginUsername, $loginUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($loginPassword, $row['password'])) {
                // Store user session variables
                $_SESSION['role'] = "student";
                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];

                // Redirect to student dashboard
                header("Location: student.html");
                exit();
            } else {
                echo "<script>alert('Invalid password!'); window.location.href = 'login.html';</script>";
            }
        } else {
            echo "<script>alert('User not found!'); window.location.href = 'login.html';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Invalid role selected!'); window.location.href = 'login.html';</script>";
    }
}

$conn->close();
?>
