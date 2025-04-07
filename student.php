<?php
session_start();
include 'db_connection.php';

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
$students = [];
while ($stmt->fetch()) {
    $students[] = ['id'=>$id,'name' => $name, 'email' => $email, 'phone' => $phone];
   
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .student-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 15px;
            width: 250px;
            text-align: center;
            border-left: 5px solid #4CAF50;
        }
        .student-card h3 {
            margin: 0;
            color: #333;
        }
        .student-card p {
            margin: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>

    <?php foreach ($students as $student): ?>
        <div class="student-card">
            <h3><?php echo htmlspecialchars($student['name']); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
