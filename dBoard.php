<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['student_logged_in'])) {
    header("Location: studentLogin.php");
    exit();
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student details from database
$student_name = $_SESSION['student_name'];

$sql = "SELECT name, email, phone FROM students WHERE name= ?";
$stmt = $conn->prepare($sql);

// Fix: Use "s" (string) instead of "i"
$stmt->bind_param("s", $student_name);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);

// Fetch the result
if ($stmt->fetch()) {
    // Successfully retrieved data
} else {
    $name = "N/A";
    $email = "N/A";
    $phone = "N/A";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-btn {
            background: #2c3e50;
            color: white;
            padding: 10px;
            width: 200px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 10px;
            transition: 0.3s;
        }
        .profile-btn:hover {
            background: #1a252f;
        }
        .profile {
            display: none;
            background: #ecf0f1;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .exam-container {
            display: flex;
            justify-content: space-between;
        }
        .exam-mode {
            width: 30%;
            padding: 20px;
            background: #8ab9d8;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .exam-mode:hover {
            background: #2980b9;
        }
        .logout-btn {
            margin-top: 20px;
            background: red;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>

        <!-- Profile Button -->
        <button class="profile-btn" onclick="toggleProfile()"> MY PROFILE</button>

        <!-- Student Profile Section (Initially Hidden) -->
        <div class="profile" id="studentProfile">
            <h2>Student Profile</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
        </div>

        <!-- Exam Modes -->
        <h2>Select Exam Mode</h2>
        <div class="exam-container">
            <div class="exam-mode" onclick="startExamLow('low')" style="color:rgb(7, 118, 7)"> Low Level</div>
            <div class="exam-mode" onclick="startExamMedium('medium')" style="color:rgb(226, 216, 16)">Medium Level</div>
            <div class="exam-mode" onclick="startExamhigh('difficult')" style="color:rgb(232, 37, 7)"> Difficult Level</div>
        </div>

        <a href="logout.php"><button class="logout-btn">Logout</button></a>
    </div>

    <script>
        function toggleProfile() {
            const profile = document.getElementById('studentProfile');
            profile.style.display = (profile.style.display === 'none' || profile.style.display === '') ? 'block' : 'none';
        }

        function startExamLow(level) {
            alert("Starting " + level + " level exam...");
            window.location.href = "lowLevel.php";
        }
        function startExamhigh(level) {
            alert("Starting " + level + " level exam...");
            window.location.href = "high.php";
        }
        function startExamMedium(level) {
            alert("Starting " + level + " level exam...");
            window.location.href = "medium.php";
        }
    </script>
</body>
</html>
