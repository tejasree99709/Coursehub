<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coursehub";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to retrieve logged-in user ID
session_start();
$current_user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

// Handle registration action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $course_id = $_POST['course_id'];
    $register_query = "INSERT INTO student_registration (user_id, course_id, is_registered) VALUES ('$current_user_id', '$course_id', 1) ON DUPLICATE KEY UPDATE is_registered = 1";
    if ($conn->query($register_query)) {
        echo "<script>alert('Registration successful!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error registering for the course.');</script>";
    }
    exit;
}

// Handle certificate upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['certificate'])) {
    $course_id = $_POST['course_id'];
    $certificate = $_FILES['certificate'];

    if ($certificate['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $certificate_path = $upload_dir . basename($certificate['name']);
        if (move_uploaded_file($certificate['tmp_name'], $certificate_path)) {
            $update_query = "UPDATE student_registration SET certificate_link = '$certificate_path' WHERE user_id = '$current_user_id' AND course_id = '$course_id'";
            if ($conn->query($update_query)) {
                echo "<script>alert('Certificate uploaded successfully!'); window.location.href = window.location.href;</script>";
            } else {
                echo "<script>alert('Error updating certificate in the database.');</script>";
            }
        } else {
            echo "<script>alert('Error moving uploaded file.');</script>";
        }
    } else {
        echo "<script>alert('File upload error.');</script>";
    }
    exit;
}

// Handle certificate deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_certificate'])) {
    $course_id = $_POST['course_id'];
    $delete_query = "UPDATE student_registration SET certificate_link = NULL WHERE user_id = '$current_user_id' AND course_id = '$course_id'";
    if ($conn->query($delete_query)) {
        echo "<script>alert('Certificate deleted successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error deleting certificate.');</script>";
    }
    exit;
}

// Fetch courses and registration status
$sql = "SELECT c.id, c.course_name, c.course_link, 
               IF(sr.is_registered = 1, 1, 0) AS is_registered, 
               sr.certificate_link
        FROM courses c
        LEFT JOIN student_registration sr 
        ON c.id = sr.course_id AND sr.user_id = '$current_user_id'";

$result = $conn->query($sql);
if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(232, 235, 238);
            color: #333;
            margin: 0;
            padding: 0;
        }
        .back-button {
            display: inline-block;
            margin: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: rgb(33, 35, 136);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        td a:hover {
            text-decoration: underline;
        }
        .register-btn, .registered-btn, .delete-btn {
            padding: 8px 12px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        .register-btn {
            background-color: #007bff;
        }
        .registered-btn {
            background-color: #28a745;
            pointer-events: none;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .upload-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
        }
        .share-btn {
            background-color: #17a2b8;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        .share-btn:hover {
            background-color: #138496;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>

    <a href="student.html" class="back-button">&larr; Go Back</a>
    <div class="container">
        <h1>Available Courses</h1>
        <table>
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Course Name</th>
                    <th>Course Link</th>
                    <th>Registration Status</th>
                    <th>Certificate</th>
                    <th>Action</th>
                    <th>Share</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sno = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $sno++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                        echo "<td><a href='" . htmlspecialchars($row['course_link']) . "' target='_blank'>View Course</a></td>";
                        echo "<td>";
                        if ($row['is_registered']) {
                            echo "<button class='registered-btn'>Registered</button>";
                        } else {
                            echo "<form method='POST'>
                                    <input type='hidden' name='course_id' value='" . $row['id'] . "'>
                                    <button type='submit' name='register' class='register-btn'>Register</button>
                                  </form>";
                        }
                        echo "</td>";
                        echo "<td>";
                        if (!empty($row['certificate_link'])) {
                            echo "<a href='" . htmlspecialchars($row['certificate_link']) . "' target='_blank'>View Certificate</a>";
                        } else {
                            echo "<form method='POST' enctype='multipart/form-data'>
                                    <input type='file' name='certificate' required>
                                    <input type='hidden' name='course_id' value='" . $row['id'] . "'>
                                    <button type='submit' class='upload-btn'>Upload</button>
                                  </form>";
                        }
                        echo "</td>";
                        echo "<td>";
                        if (!empty($row['certificate_link'])) {
                            echo "<form method='POST'>
                                    <input type='hidden' name='course_id' value='" . $row['id'] . "'>
                                    <button type='submit' name='delete_certificate' class='delete-btn'>Delete</button>
                                  </form>";
                        }
                        echo "</td>";
                        echo "<td>";
                        if (!empty($row['certificate_link'])) {
                            $shareLink = urlencode($row['certificate_link']);
                            echo "<div class='dropdown'>
                                    <button class='share-btn'>Share</button>
                                    <div class='dropdown-content'>
                                        <a href='https://api.whatsapp.com/send?text=Check%20out%20this%20certificate:%20$shareLink' target='_blank'>WhatsApp</a>
                                        <a href='https://www.linkedin.com/shareArticle?url=$shareLink' target='_blank'>LinkedIn</a>
                                    </div>
                                  </div>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No courses available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>