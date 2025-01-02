<?php
// Database connection
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "coursehub"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$feedback_message = ""; // Holds success or error message
$success = false; // Flag to indicate success
$form_submitted = false; // Flag to check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course_name'], $_POST['course_link'])) {
    $course_name = trim($_POST['course_name']);
    $course_link = trim($_POST['course_link']);

    // Validate inputs
    if (!empty($course_name) && !empty($course_link)) {
        // Check for duplicate course name
        $check_query = $conn->prepare("SELECT COUNT(*) FROM courses WHERE course_name = ?");
        $check_query->bind_param("s", $course_name);
        $check_query->execute();
        $check_query->bind_result($count);
        $check_query->fetch();
        $check_query->close();

        if ($count > 0) {
            $feedback_message = "Error: Course name already exists.";
        } else {
            // Insert the new course
            $stmt = $conn->prepare("INSERT INTO courses (course_name, course_link) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("ss", $course_name, $course_link);

                if ($stmt->execute()) {
                    $feedback_message = "Course added successfully!";
                    $success = true; // Set success flag to true
                    $form_submitted = true; // Form is submitted successfully
                } else {
                    $feedback_message = "Error: Could not add course. " . $stmt->error;
                }

                $stmt->close();
            } else {
                $feedback_message = "Error preparing statement: " . $conn->error;
            }
        }
    } else {
        $feedback_message = "All fields are required.";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Course</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        form {
            margin-bottom: 20px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            margin-top: 10px;
            font-size: 16px;
            color:rgb(39, 82, 174);
        }
        .error {
            color: #e74c3c;
        }
        .form-container {
            display: <?php echo $form_submitted ? 'none' : 'block'; ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Post Course</h1>
        <!-- Only show form if it's not submitted -->
        <div class="form-container">
            <form method="POST">
                <input type="text" name="course_name" placeholder="Course Name" required>
                <input type="url" name="course_link" placeholder="Course Link" required>
                <button type="submit">Post Course</button>
            </form>
        </div>

        <?php if (!empty($feedback_message)) : ?>
            <!-- Show feedback message if any -->
            <div class="message <?php echo (strpos($feedback_message, 'Error') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        <?php if ($success): ?>
            // Display success pop-up message and redirect to postcourses.html
            alert('Course added successfully!');
            window.location.href = "post_courses.html"; // Redirect after clicking OK
        <?php elseif (!empty($feedback_message)): ?>
            // Display error pop-up message
            alert('<?php echo htmlspecialchars($feedback_message); ?>');
        <?php endif; ?>
    </script>
</body>
</html>