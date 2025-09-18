<?php
session_start();
include 'config.php';

// Register
if (isset($_POST["register"])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $check_query = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

// Login
if (isset($_POST["login"])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT password FROM users WHERE username=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['password']) {
            $_SESSION['username'] = $username;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}

// Logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!-- HTML Section -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Register</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 50px; text-align: center; }
        .container { background: #fff; padding: 20px; border-radius: 10px; width: 350px; margin: auto; box-shadow: 0 0 10px gray; }
        input { width: 90%; padding: 8px; margin: 6px 0; border-radius: 4px; border: 1px solid gray; }
        .btn { background: blue; color: white; padding: 10px; border: none; width: 100%; cursor: pointer; }
        .btn:hover { background: darkblue; }
        .logout-btn { background: red; padding: 8px; color: white; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #007BFF; color: white; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['username'])): ?>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>You have successfully logged in.</p>
        <a href="index.php?logout=true" class="logout-btn">Logout</a>

        <?php if ($_SESSION['username'] === 'admin'): ?>
            <h2>👥 Registered Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                </tr>
                <?php
                $result = $conn->query("SELECT id, username FROM users");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . htmlspecialchars($row['id']) . "</td><td>" . htmlspecialchars($row['username']) . "</td></tr>";
                }
                ?>
            </table>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <input type="submit" class="btn" name="login" value="Login">
        </form>

        <h2>Register</h2>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter Username" required><br>
            <input type="password" name="password" placeholder="Enter Password" required><br>
            <input type="submit" class="btn" name="register" value="Register">
        </form>
    </div>
<?php endif; ?>

</body>
</html>
