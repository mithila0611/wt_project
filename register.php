<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO students (name, email, phone, password,section) VALUES ('$name', '$email', '$phone', '$hashed_password','$section')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['student_logged_in'] = true;
        $_SESSION['student_name'] = $name;
        $_SESSION['student_email'] = $email;
        header("Location: dBoard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
