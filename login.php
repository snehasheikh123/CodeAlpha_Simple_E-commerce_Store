
<?php
$_SESSION['user_id'] = $row['id'];
$_SESSION['user_name'] = $row['name'];

include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='error'>❌ Invalid password!</div>";
        }
    } else {
        echo "<div class='error'>⚠️ No user found with this email!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-image: url("images/bg_6.jpg");
      background-size: cover;
      background-position: center;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    form {
      width: 320px;
      background-color: #ffffffee;
      backdrop-filter: blur(12px);
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      padding: 30px 25px;
      display: flex;
      flex-direction: column;
      align-items: center;
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    h2 {
      margin-bottom: 30px;
      font-size: 24px;
      color: #333;
    }

    .input-group {
      width: 100%;
      margin-bottom: 20px;
      position: relative;
    }

    .input-group span.icon {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 18px;
      color: #555;
    }

    .input-group input {
      width: 100%;
      padding: 12px 38px 12px 38px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }

    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 16px;
      color: #555;
      user-select: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #c2a942;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #a88d35;
    }

    .register-link {
      margin-top: 20px;
      font-size: 14px;
      color: #333;
    }

    .register-link a {
      color: #c2a942;
      text-decoration: none;
      font-weight: bold;
    }

    .error {
      text-align: center;
      color: red;
      font-weight: 500;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<form method="POST" action="">
  <h2>🔐 Login</h2>

  <div class="input-group">
    <span class="icon">📧</span>
    <input type="email" name="email" placeholder="Email Address" required>
  </div>

  <div class="input-group">
    <span class="icon">🔑</span>
    <input type="password" name="password" id="password" placeholder="Password" required>
    <span class="toggle-password" onclick="togglePassword()">👁</span>
  </div>

  <button type="submit">🚀 Login</button>

  <div class="register-link">
    Don't have an account? <a href="register.php">Register</a>
  </div>
</form>

<script>
function togglePassword() {
  const passwordInput = document.getElementById("password");
  const toggle = document.querySelector(".toggle-password");
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    toggle.textContent = "🙈";
  } else {
    passwordInput.type = "password";
    toggle.textContent = "👁";
  }
}
</script>

</body>
</html>
