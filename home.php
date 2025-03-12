<?php
session_start();

// If already logged in, redirect to portal
if (isset($_SESSION['user'])) {
    header("Location: portal.php");
    exit();
}

// Include database connection
include('database.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Define queries for each role
    $roles = [
        'engineer' => "SELECT * FROM Engineer WHERE engineerUsername = ? AND engineerPassword = ?",
        'admin'    => "SELECT * FROM Administrator WHERE adminUsername = ? AND adminPassword = ?",
        'manager'  => "SELECT * FROM Manager WHERE managerUsername = ? AND managerPassword = ?",
        'sales'    => "SELECT * FROM SalesTeam WHERE salesUsername = ? AND salesPassword = ?"
    ];

    // Check if we have a valid connection
    if ($conn) {
        foreach ($roles as $role => $query) {
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param('ss', $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    // Valid user found
                    $_SESSION['user'] = $result->fetch_assoc();
                    $_SESSION['role'] = $role;
                    header("Location: portal.php");
                    exit();
                }
                $stmt->close();
            }
        }
    }

    // If no match found or connection invalid, show generic error
    $error = 'Invalid login credentials';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CRM Login</title>
  <link rel="stylesheet" href="home.css">
</head>
<body>
  <header>
    <h1>CRM System Portal</h1>
  </header>
  <main>
    <div class="login-container">
      <h2>Login</h2>
      <?php if ($error !== ''): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
      <form method="post" action="home.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
      </form>
    </div>
  </main>
  <footer>
    <p>&copy; <?php echo date("Y"); ?> Your Company. All rights reserved.</p>
  </footer>
</body>
</html>
