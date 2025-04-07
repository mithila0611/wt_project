<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Signup</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-image: url("stuSign.jpg"); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .container { width: 350px; margin: auto; padding: 20px; height: 100vh;}
        input { width: 100%; padding: 8px; margin: 8px 0; }
        button { width: 100%; padding: 10px; background: green; color: white; cursor: pointer; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Student Signup</h2>
        <form action="register.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="section" placeholder="Section" required>
            <button type="submit">Sign Up</button>
        </form>
        <p><a href="studentLogin.php">Already have an account? Login</a></p>
    </div>

</body>
</html>
