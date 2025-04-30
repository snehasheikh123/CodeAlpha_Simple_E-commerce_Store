<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-image: url("images/bg_6.jpg");
      background-size: cover;
      background-position: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    form {
      width: 340px;
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

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 18px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
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

    .login-link {
      margin-top: 15px;
      font-size: 14px;
      color: #333;
    }

    .login-link a {
      color: #c2a942;
      text-decoration: none;
      font-weight: bold;
    }

    .error {
      color: red;
      margin-bottom: 10px;
      text-align: center;
    }
  </style>
</head>
<body>

<form method="POST" action="">
  <h2>📝 Register</h2>
  
  <input type="text" name="name" placeholder="Full Name" required>
  
  <input type="email" name="email" placeholder="Email Address" required>
  <input type="password" name="password" placeholder="Password" required>
  <input type="password" name="confirm_password" placeholder="Confirm Password" required>
  
  <button type="submit">Register</button>

  <div class="login-link">
    Already have an account? <a href="login.php">Login</a>
  </div>
</form>

</body>
</html>
