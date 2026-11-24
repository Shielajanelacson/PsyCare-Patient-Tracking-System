<?php
$publicPage = true;
include 'db.php';

if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$notice = isset($_GET['registered']) ? 'Account created successfully. Please log in.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    if ($u && password_verify($_POST['password'], $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['full_name'] = $u['full_name'];
        $_SESSION['username'] = $u['username'];
        header("Location: index.php");
        exit;
    }
    $error = 'Invalid username/email or password.';
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - PsyCare</title>
  <script>
    (function () {
      try {
        var t = localStorage.getItem('psycare-theme');
        if (t) document.documentElement.setAttribute('data-bs-theme', t);
      } catch (e) {}
    })();
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital@1&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-brand">
    <div class="auth-logo"><i class="bi bi-heart-pulse-fill"></i>PsyCare</div>
    <div class="auth-tagline">Choose PsyCare because your mind deserves care.</div>
    <div class="auth-line"></div>
    <p class="auth-desc">A secure patient management system built for psychology practitioners.</p>
    <ul class="auth-points">
      <li><i class="bi bi-check-circle-fill"></i>Patient records and disorder updates</li>
      <li><i class="bi bi-check-circle-fill"></i>Appointment scheduling with room assignment</li>
      <li><i class="bi bi-check-circle-fill"></i>Clinical notes and a complete activity history</li>
        </ul>
    <div class="auth-brain"><?php include 'brain.php'; ?></div>
  </div>

  <div class="auth-form-side">
    <div class="auth-card">
      <h2>Welcome back</h2>
      <p class="auth-sub">Log in to continue to your dashboard.</p>

      <?php if ($notice): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($notice) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username or Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" name="login" class="form-control" placeholder="Enter username or email" required autofocus>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            <button type="button" class="btn-eye" data-target="password"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button class="btn btn-primary btn-auth"><i class="bi bi-box-arrow-in-right me-1"></i>Log In</button>
      </form>

      <div class="auth-foot">Don't have an account? <a href="register.php">Create one</a></div>
      <div class="auth-mini"><i class="bi bi-shield-lock me-1"></i>Confidential. For authorized practitioners only.</div>
    </div>
  </div>
</div>
<script>
document.querySelectorAll('.btn-eye').forEach(function (b) {
  b.addEventListener('click', function () {
    const input = document.getElementById(b.dataset.target);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    b.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
  });
});
</script>
<?php include 'cat.php'; ?>
</body>
</html>