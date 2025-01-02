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

// Fetch courses and the number of registered students
$course_query = "SELECT c.id, c.course_name, COUNT(sr.user_id) AS total_students 
                 FROM courses c 
                 LEFT JOIN student_registration sr ON c.id = sr.course_id
                 WHERE sr.is_registered = 1
                 GROUP BY c.id, c.course_name";
$course_result = $conn->query($course_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color:rgb(232, 235, 238);
        }
        .main-content {
            padding: 20px;
            text-align: center;
        }
        .header {
            background-color:rgb(48, 126, 214);
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 10px;
            position: relative;
        }
        .header .back-btn {
            position: absolute;
            top: 15px;
            left: 8px;
            text-decoration: none;
            color: #151414;
            font-size: 24px;
            display: flex;
            align-items: center;
            font-weight: bold;
        }
        .header .back-btn:hover {
            background-color:rgb(47, 115, 188);
        }
        
        .header .back-btn span {
            margin-left: 8px;
            font-size: 16px;
        }
        table {
            margin: 0 auto;
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #dee2e6;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            background-color:rgb(138, 180, 225);
            font-size: 16px;
            color:black;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn:hover {
            background-color:rgb(237, 241, 245);
        }
        #studentModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            width: 50%;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function showStudents(courseId) {
            fetch(`get_students.php?course_id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    const studentTableBody = document.getElementById('studentTableBody');
                    studentTableBody.innerHTML = ''; // Clear previous data

                    if (data.length > 0) {
                        data.forEach(student => {
                            const certificateLink = student.certificate_link === 'No Certificate' 
                                ? student.certificate_link 
                                : `<a href="${student.certificate_link}" target="_blank">View Certificate</a>`;

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${student.username}</td>
                                <td>${student.email}</td>
                                <td>${certificateLink}</td>
                            `;
                            studentTableBody.appendChild(row);
                        });
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = '<td colspan="3">No students registered for this course.</td>';
                        studentTableBody.appendChild(row);
                    }

                    document.getElementById('studentModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching students:', error);
                    alert('An error occurred while fetching students.');
                });
        }

        function closeStudentModal() {
            document.getElementById('studentModal').style.display = 'none';
            document.getElementById('studentTableBody').innerHTML = ''; // Clear modal content
        }
    </script>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <a href="Admin.html" class="back-btn">&#8592;<span>Go Back</span></a>
            <h1>Status</h1>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Total Students Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody style="background-color:#f8f9fa">
                <?php if ($course_result->num_rows > 0) {
                    while ($course = $course_result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($course['course_name']) . "</td>
                                <td>" . htmlspecialchars($course['total_students']) . "</td>
                                <td>
                                    <button class='btn' onclick='showStudents(" . intval($course['id']) . ")'>View</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No courses found.</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>

    <!-- Student Modal -->
    <div id="studentModal">
        <h3>Registered Students</h3>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <!-- Student data will be dynamically added here -->
            </tbody>
        </table>
        <button class="btn" style="margin-top:10px;" onclick="closeStudentModal()">Close</button>
    </div>
</body>
</html>
<?php $conn->close(); ?>