<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['student_logged_in']) || !isset($_SESSION['student_name'])) {
    echo "User not logged in";
    exit();
}

$student_name = $_SESSION['student_name']; 
$score = $_POST['score'];
$time_taken = $_POST['time_taken'];

// Fetch existing attempts and scores
$query = "SELECT score, attempts FROM quiz_results WHERE student_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $previous_total_score = $row['score'] * $row['attempts'];  // Calculate total previous score
    $attempts = $row['attempts'] + 1;  // Increase attempt count

    // Calculate new average score
    $new_average_score = ($previous_total_score + $score) / $attempts;

    // Update the existing record with the new average score
    $update_query = "UPDATE quiz_results SET score = ?, time_taken = ?, attempts = ?, attempt_date = NOW() WHERE student_name = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("diis", $new_average_score, $time_taken, $attempts, $student_name);
    $update_stmt->execute();

    echo "Updated average score: " . number_format($new_average_score, 2);
} else {
    // First attempt: Insert new record
    $attempts = 1;
    $insert_query = "INSERT INTO quiz_results (student_name, attempts, score, time_taken, attempt_date) VALUES (?, ?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("siid", $student_name, $attempts, $score, $time_taken);
    $insert_stmt->execute();

    echo "New score recorded: " . number_format($score, 2);
}

$stmt->close();
$conn->close();
?>
