<?php
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
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    form {
      width: 320px;
      height: 340px;
      background-color: #ffffffcc;
      backdrop-filter: blur(10px);
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: stretch;
      animation: popUp 0.8s ease forwards;
      opacity: 0;
    }

    @keyframes popUp {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    form h2 {
      text-align: center;
      color: #333;
      margin-bottom: 25px;
    }

    .input-group {
      position: relative;
      margin-bottom: 18px;
    }

    .input-group span {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      font-size: 18px;
    }

    .input-group input {
      width: 80%;
      padding: 10px 10px 10px 38px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      background-color: #fff;
    }

    button {
      padding: 10px;
      background-color: #c2a942;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 8px;
    }

    button:hover {
      background-color: #aa902e;
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
    <span>✉️</span>
    <input type="email" name="email" placeholder="Email Address" required>
  </div>
  <div class="input-group">
    <span>🔒</span>
    <input type="password" name="password" placeholder="Password" required>
  </div>
  <button type="submit">👉 Login</button>
</form>

</body>
</html>
