<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mome Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="View/Login.css">
</head>
<body>

  <div class="login-container">
    <div class="left-side d-none d-md-block"></div>

    <div class="right-side d-flex flex-column justify-content-center align-items-center text-center px-4">
      <img src="Images/Logo.png" class="img-fluid mb-3" style="max-width: 170px;">

      <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger w-100 text-center" role="alert">
          Invalid username or password.
        </div>
      <?php endif; ?>

      <form method="POST" action="index.php?c=Login&m=verifikasiData" class="w-100" style="max-width: 360px;">
        <div class="mb-3 text-start">
          <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
          </div>
        </div>

        <div class="mb-3 text-start">
          <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            <span class="input-group-text toggle-password">
              <i class="bi bi-eye" id="toggleIcon"></i>
            </span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>

        <p class="mt-3 small text-center">
          Don't have an account? <a href="index.php?c=Register&m=index" class="register-link">Register now</a>
        </p>
      </form>
    </div>
  </div>

  <script>
    document.querySelector('.toggle-password').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = document.getElementById('toggleIcon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    });
  </script>

</body>
</html>
