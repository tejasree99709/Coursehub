<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coursehub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    
    $query = "SELECT u.username, u.email, 
                     CASE 
                         WHEN sr.certificate_link IS NULL OR sr.certificate_link = '' 
                         THEN 'No Certificate' 
                         ELSE sr.certificate_link 
                     END AS certificate_link
              FROM users u
              INNER JOIN student_registration sr ON u.id = sr.user_id
              WHERE sr.course_id = ? AND sr.is_registered = 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'username' => $row['username'],
            'email' => $row['email'],
            'certificate_link' => $row['certificate_link']
        ];
    }
    
    echo json_encode($students);
} else {
    echo json_encode([]);
}

$conn->close();
?>