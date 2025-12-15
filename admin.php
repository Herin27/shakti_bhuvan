<?php
session_start();

// Handle login submission
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check credentials
    if ($email === "admin123@gmail.com" && $password === "1") {
        $_SESSION['admin'] = $email;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #fffdf9;
      text-align: center;
      padding: 60px 20px;
      color: #444;
    }

    .login-box {
      background: #fff;
      padding: 40px 30px;
      width: 380px;
      margin: auto;
      margin-top: 70px;
      border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.08);
      border: 1px solid #f3e8c9;
    }

    h2 {
      color: #d4af37;
      font-size: 28px;
      margin-bottom: 5px;
      font-weight: 600;
    }

    .subtitle {
      font-size: 14px;
      color: #777;
      margin-bottom: 25px;
    }

    .welcome {
      font-size: 18px;
      font-weight: 600;
      color: #d4af37;
      margin-bottom: 20px;
    }

    input {
      width: 93%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-size: 14px;
    }

    input:focus {
      outline: none;
      border-color: #d4af37;
      box-shadow: 0px 0px 5px rgba(212,175,55,0.4);
    }

    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      margin-bottom: 20px;
    }

    .options a {
      color: #d4af37;
      text-decoration: none;
    }

    button {
      width: 100%;
      background: linear-gradient(90deg, #f1c40f, #d4af37);
      border: none;
      padding: 12px;
      color: white;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: 0.3s;
    }

    button:hover {
      background: linear-gradient(90deg, #d4af37, #f1c40f);
    }

    .admin-only {
      margin-top: 15px;
      font-size: 12px;
      font-weight: bold;
      color: #aaa;
      letter-spacing: 1px;
    }

    .demo {
      margin-top: 10px;
      font-size: 12px;
      color: #999;
    }

    .error {
      color: red;
      margin-top: 10px;
      font-size: 14px;
    }

    .back-link {
      margin-top: 30px;
      display: block;
      font-size: 14px;
      color: #999;
      text-decoration: none;
    }
    .back-link:hover {
      color: #d4af37;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Shakti Bhuvan</h2>
    <div class="subtitle">Admin Panel</div>

    <div class="welcome">Welcome Back</div>
    <p style="font-size:13px; color:#777; margin-bottom:20px;">Enter your credentials to access the admin dashboard</p>

    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email Address" required><br>
      <input type="password" name="password" placeholder="Enter your password" required><br>

      <div class="options">
        <!-- <label><input type="checkbox" name="remember"> Remember me</label> -->
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit">Sign In</button>
    </form>

    <?php if ($error): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <div class="admin-only">ADMIN ACCESS ONLY</div>
    <!-- <div class="demo">Demo credentials: admin@shaktibhuvan.com / admin123</div> -->
  </div>

  <a href="index.php" class="back-link">← Back to Website</a>
</body>
</html>
