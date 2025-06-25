<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>VoyageVista Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="style6.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
    }
    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .form-box {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }
    .form-box h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .form-box input {
      width: 100%;
      padding: 12px;
      margin: 8px 0 20px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .form-box button {
      width: 100%;
      padding: 12px;
      background-color: #321fdd;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }
    .form-box button:hover {
      background-color: #170dd1;
    }
    .form-box .link {
      text-align: center;
      margin-top: 10px;
    }
    .form-box .link a {
      color: #333;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <main>
    <div class="container">
      <div class="form-box" id="formBox">
        
      </div>
    </div>
  </main>

  <script>
    function loadLoginForm() {
      document.getElementById("formBox").innerHTML = `
        <form>
          <h2>Login</h2>
          <label for="email">Email</label>
          <input type="email" id="email" placeholder="Enter your email" required>

          <label for="password">Password</label>
          <input type="password" id="password" placeholder="**" required>

          <div class="link">
            <a href="#" onclick="loadForgotForm()">Forget Password?</a>
          </div>

          <button type="submit">LOGIN</button>

          <div class="link">
            <p>Or <a href="#">Create an Account</a></p>
          </div>
        </form>
      `;
    }

    function loadForgotForm() {
      document.getElementById("formBox").innerHTML = `
        <form onsubmit="return resetPassword(event)">
          <h2>Reset Password</h2>

          <label for="forgotEmail">Email</label>
          EMAIL:<input type="email" id="forgotEmail" name="email" required>

          <label for="newPassword">New Password</label>
          NEW PASSWORD:<input type="password" id="newPassword" name="newPassword" required>

          <label for="confirmPassword">Confirm Password</label>
          CONFIRM PASSWORD:<input type="password" id="confirmPassword" name="confirmPassword" required>

          <button type="submit">Reset Password</button>

          <div class="link">
            <a href="#" onclick="loadLoginForm()">Back to Login</a>
          </div>
        </form>
      `;
    }

    function resetPassword(event) {
      event.preventDefault();
      const email = document.getElementById("forgotEmail").value;
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (newPassword !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
      }

      
      alert("Password has been reset successfully for " + email);
      loadLoginForm(); 
    }

    
    loadLoginForm();
  </script>

</body>
</html>