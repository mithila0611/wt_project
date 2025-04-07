<?php
session_start(); 
include 'db_connection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php"); 
    exit();
}

$username = $_SESSION['username'];
$count = 0;

$sql = "SELECT id,name,email,phone
        FROM students";

$stmt = $conn->prepare($sql);

// Debugging: Check if SQL query preparation failed
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->execute();
$stmt->bind_result($id,$name, $email, $phone);
$count=0;
$stud = [];
while ($stmt->fetch()) {
    $stud[] = ['id'=>$id,'name' => $name, 'email' => $email, 'phone' => $phone];
   $count++;
}

$stmt->close();



// Fetch Low Level Quiz Results
$sql = "SELECT students.name, quiz_results.attempts, quiz_results.score, quiz_results.attempt_date 
        FROM students 
        JOIN quiz_results ON students.name = quiz_results.student_name";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->execute();
$stmt->bind_result($name, $attempted, $grade, $date);

$students = [];
while ($stmt->fetch()) {
    $students[] = ['name' => $name, 'attempted' => $attempted, 'grade' => $grade, 'date'=> $date];
    
}
$stmt->close();

// Fetch Medium Level Quiz Results
$sql = "SELECT students.name, quiz_resultsmedium.attempts, quiz_resultsmedium.score, quiz_resultsmedium.attempt_date 
        FROM students 
        JOIN quiz_resultsmedium ON students.name = quiz_resultsmedium.student_name";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->execute();
$stmt->bind_result($name, $attempted, $grade, $date);

$studentsmedium = [];
while ($stmt->fetch()) {
    $studentsmedium[] = ['name' => $name, 'attempted' => $attempted, 'grade' => $grade, 'date'=> $date];
    
}
$stmt->close();

// Fetch High Level Quiz Results
$sql = "SELECT students.name, quiz_resultshigh.attempts, quiz_resultshigh.score, quiz_resultshigh.attempt_date 
        FROM students 
        JOIN quiz_resultshigh ON students.name = quiz_resultshigh.student_name";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->execute();
$stmt->bind_result($name, $attempted, $grade, $date);

$studentshigh = [];
while ($stmt->fetch()) {
    $studentshigh[] = ['name' => $name, 'attempted' => $attempted, 'grade' => $grade, 'date'=> $date];
    
}



$sql = "SELECT id, name, email, phone, section FROM students";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->execute();
$stmt->bind_result($id, $name, $email, $phone, $section);
$count=0;
$students_by_section = [];
while ($stmt->fetch()) {
    $students_by_section[$section][] = ['id'=>$id, 'name' => $name, 'email' => $email, 'phone' => $phone, 'section' => $section];
   $count++;
}
$stmt->close();

$conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="online exam/styles.css">
</head>
<body>
    <style>
    body{
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        height: 100vh;
    }
    .dashboard-container {
        display: flex;
        width: 100%;
    }
    .sidebar {
        height:250%;
        background-color: #2f2f38;
        color: #fff;
        width: 250px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .sidebar h2 {
        margin-bottom: 20px;
        font-size: 24px;
    }
    .admin-info img {
        width: 80px;
        border-radius: 50%;
    }
    .admin-info h3 {
        margin-top: 10px;
    }
    .sidebar nav a {
        display: block;
        color: #bbb;
        text-decoration: none;
        padding: 10px 0;
        width: 100%;
        text-align: center;
    }
    .sidebar nav a:hover {
        background-color: #444;
    } 
    .main-content {
        flex-grow: 1;
        padding: 20px;
        background: #fff;
    } 
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    } 
    .logout {
        background-color: #00bcd4;
        color: #fff;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
    }
    .stats {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    .card {
        background-color: #4caf50;
        color: #fff;
        padding: 20px;
        flex: 1;
        text-align: center;
        border-radius: 8px;
        font-size: 18px;
        font-weight: bold;
    } 
    .card:nth-child(2) { background-color: #f44336; }
    .card:nth-child(3) { background-color: #795548; }
    .question-control {
        margin-top: 20px;
    }
    #newQuestion {
        padding: 10px;
        width: 300px;
        margin-right: 10px;
    } 
    button {
        background-color: #4caf50;
        color: #fff;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 5px;
    }
    ul {
        list-style: none;
        padding: 0;
    }
    li {
        background: #eee;
        padding: 10px;
        margin: 5px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    li button {
        background-color: #f44336;
        padding: 5px 10px;
    }
    
    table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        </style>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h2>ONLINE QUIZ</h2>
            <div class="admin-info">
                <img src="logo.png" alt="Admin Avatar">
                <h3>Admin</h3>
            </div>
            <nav>
                <a href="#">Dashboard</a>
                <a href="studentinfoHigh.php">Students Results(High)</a>
                <a href="studentinfoMedium.php">Students Results(Medium)</a>
                <a href="studentinfo.php">Students Results(Low)</a>
                <a href="student.php">Users</a>
            </nav>
        </aside>
        <main class="main-content">
            <div class="header">
                <h1>Admin <?php echo $username; ?></h1>
                <a href="logout.php"><button class="logout">Logout</button></a>
            </div>
            <div class="stats">
                <div class="card">Total Students: <?php echo $count; ?></div>
                <div class="card">General Knowledge Questions</div>
                <div class="card">Total Levels: 3</div>
            </div>
            <div class="question-control">
                <h2>Add Students</h2>
                
               <a href="studentadd.php" ><button >Add Student</button></a>
            </div>
            <div class="user-performance">
                <h2>Students Grouped by Section</h2>
                <?php foreach ($students_by_section as $section => $students): ?>
                    <h3>Section: <?php echo htmlspecialchars($section); ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Section</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                <td><?php echo htmlspecialchars($student['section']); ?></td>
                                <td>
                                    <form method="POST" action="delete_student.php" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        <input type="hidden" name="student_name" value="<?php echo htmlspecialchars($student['name']); ?>">
                                        <button type="submit" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
           

                

                <h2>User Performance in Medium</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Attempted</th>
                            <th>Grade</th>
                            <th>date</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($studentsmedium as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['attempted']); ?></td>
                            <td><?php echo htmlspecialchars($student['grade']); ?></td>
                            <td><?php echo htmlspecialchars($student['date']); ?></td>
                            <td>
                                <form method="POST" action="delete_student.php" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="student_name" value="<?php echo htmlspecialchars($student['name']); ?>">
                                    <button type="submit" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


                <h2>User Performance in High</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Attempted</th>
                            <th>Grade</th>
                            <th>date</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($studentshigh as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['attempted']); ?></td>
                            <td><?php echo htmlspecialchars($student['grade']); ?></td>
                            <td><?php echo htmlspecialchars($student['date']); ?></td>
                            <td>
                                <form method="POST" action="delete_student.php" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="student_name" value="<?php echo htmlspecialchars($student['name']); ?>">
                                    <button type="submit" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>  