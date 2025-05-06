<?php

session_start();

// SEND TO PORTAL IG LOGGED IN
if (isset($_SESSION['user'])) {
    header("Location: portal.php");
    exit();
}

// DB CONNECTION CONNECT 
include('database.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // EACH ROLE QUERYING
    $roles = [
        'engineer' => "SELECT * FROM Engineer WHERE engineerUsername = ? AND engineerPassword = ?",
        'admin'    => "SELECT * FROM Administrator WHERE adminUsername = ? AND adminPassword = ?",
        'manager'  => "SELECT * FROM Manager WHERE managerUsername = ? AND managerPassword = ?",
        'sales'    => "SELECT * FROM SalesTeam WHERE salesUsername = ? AND salesPassword = ?"
    ];

    // CONNECTION CHECKING - SEND TO PORTAL.PHP
    if ($conn) {
        foreach ($roles as $role => $query) {
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param('ss', $username, $password);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    // THEN THE VALID CREDENTIAL IS VALID
                    $_SESSION['user'] = $result->fetch_assoc();
                    $_SESSION['role'] = $role;
                    header("Location: portal.php");
                    exit();
                }
                $stmt->close();
            }
        }
    }

    // SIMPLE ERROR STATEMENT
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
      <form method="post" action="index.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
      </form>
    </div>
  </main>
  <footer>
    <p>&copy; <?php echo date("Y"); ?> BA Security. All rights reserved.</p>
  </footer>
  <script src="alerts.js"></script>
</body>
</html>
