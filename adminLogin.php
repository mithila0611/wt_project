
<?php
session_start();
include 'db_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['password'] === $password) { 
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['username'] = $username; 
                header("Location: admin_dBoard.php"); 
                echo "succesful";
                exit();
            } else {
                $error_message = "Invalid login credentials.";
            }
        } else {
            $error_message = "Invalid login credentials.";
        }
    } else {
        $error_message = "Database query failed: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif; text-align: center; padding: 20px;
            background-image: url('adminLogin.webp');
            background-position: center;
            background-repeat: no-repeat; 
            background-size: 100% 100%;
            min-height: 100vh;
        }
        .container {
            width: 350px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: darkred;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: red;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Admin Login</h2>
        <?php if (isset($error_message)) { ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php } ?>
        <form  action="adminLogin.php" method="POST">
            <input type="text"  placeholder="Admin Email" name="username" required>
            <div id="adminEmailError" class="error"></div>

            <input type="password"  placeholder="Password" name="password" required>
            <div id="adminPasswordError" class="error"></div>

            <button type="submit">Login</button>
        </form>
    </div>


</body>
</html>
