<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_name'])) {
    $student_name = $_POST['student_name'];

    // Delete student from both tables
    $sql1 = "DELETE FROM quiz_results WHERE student_name = ?";
    $sql2 = "DELETE FROM students WHERE name = ?";

    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $student_name);
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $student_name);

    if ($stmt1->execute() && $stmt2->execute()) {
        echo "<script>alert('Student deleted successfully!'); window.location.href='admin_dBoard.php';</script>";
    } else {
        echo "<script>alert('Error deleting student!'); window.history.back();</script>";
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
}
?>
