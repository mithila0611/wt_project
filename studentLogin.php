<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Login</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; background-image : url('bg.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;}
        .container { width: 350px; margin: auto; padding: 20px;  
            height:100vh;
        }
        input { width: 100%; padding: 8px; margin: 8px 0; }
        button { width: 100%; padding: 10px; background: blue; color: white; cursor: pointer; border-color: transparent; }
        .sign-style{color: yellow;}
    </style>
</head>
<body>

    
    <div class="container">
        <h2 class="sign-style">Student Login</h2>
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p><a href="studentSignup.php" class="sign-style">Don't have an account? Sign up</a></p>
    </div>

</body>
</html>0
