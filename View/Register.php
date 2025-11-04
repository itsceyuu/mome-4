<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mome Register</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="View/Register.css">
</head>
<body>

  <div class="register-container">
    <div class="left-side d-none d-md-block"></div>

    <div class="right-side d-flex justify-content-center align-items-center">
      <div class="register-box">
        <div class="text-center mb-3">
          <img src="Images/Logo.png" class="img-fluid" style="max-width: 170px;">
        </div>

        <?php if(isset($_GET['error'])): ?>
          <div class="alert alert-danger text-center" role="alert">
            Registration failed. Please check your input.
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?c=Register&m=proses">
          <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
          </div>

          <div class="mb-3">
            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
              <span class="input-group-text toggle-password">
                <i class="bi bi-eye" id="toggleIcon"></i>
              </span>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 mt-3">Register Now</button>

          <p class="text-center mt-3 small">
            Already have an account?
            <a href="index.php?c=Login&m=index" class="login-link">Login here</a>
          </p>
        </form>
      </div>
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
