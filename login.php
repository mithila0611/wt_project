<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM students WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_logged_in'] = true;
            $_SESSION['student_name'] = $row['name'];
            $_SESSION['student_email'] = $row['email'];
            header("Location: dBoard.php");
            exit();
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>
