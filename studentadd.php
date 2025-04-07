<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Signup</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .container { width: 350px; margin: auto; padding: 20px; }
        input { width: 100%; padding: 8px; margin: 8px 0; }
        button { width: 100%; padding: 10px; background: green; color: white; cursor: pointer; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Student Add</h2>
        <form action="adminstudentadd.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Add Student</button>
        </form>
    </div>

</body>
</html>
