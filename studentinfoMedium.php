<?php
session_start();
include 'db_connection.php';

$sql = "SELECT students.name, quiz_resultsmedium.attempts, quiz_resultsmedium.score,quiz_resultsmedium.attempt_date 
        FROM students 
        JOIN quiz_resultsmedium ON students.name = quiz_resultsmedium.student_name";

$stmt = $conn->prepare($sql);

// Debugging: Check if SQL query preparation failed
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->execute();
$stmt->bind_result($name, $attempted, $grade,$date);

$students = [];
while ($stmt->fetch()) {
    $students[] = ['name' => $name, 'attempted' => $attempted, 'grade' => $grade, 'date'=> $date];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
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
</head>
<body>

    <h2>Student Quiz Results</h2>
    
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Attempts</th>
                <th>Score</th>
                <th>Attempt Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['attempted']); ?></td>
                    <td><?php echo htmlspecialchars($student['grade']); ?></td>
                    <td><?php echo htmlspecialchars($student['date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>

