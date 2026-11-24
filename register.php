<?php
$publicPage = true;
include 'db.php';

if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$old = ['full_name' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full = trim($_POST['full_name']);
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $conf = $_POST['confirm'];
    $old = ['full_name' => $full, 'username' => $user, 'email' => $email];

    if ($full === '' || mb_strlen($full) > 100) {
        $errors[] = 'Please enter your full name.';
    }
    if (!preg_match('/^[A-Za-z0-9_.]{4,30}$/', $user)) {
        $errors[] = 'Username must be 4 to 30 characters: letters, numbers, dot or underscore.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($pass) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($pass !== $conf) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $chk = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $chk->bind_param("ss", $user, $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $errors[] = 'That username or email is already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password_hash) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $full, $user, $email, $hash);
            $stmt->execute();
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - PsyCare</title>
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
      <h2>Create your account</h2>
      <p class="auth-sub">Register to start managing your patients.</p>

      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $e): ?>
            <div><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($e) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="regForm">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
            <input type="text" name="full_name" class="form-control" placeholder="e.g. Doctor Lacson" value="<?= htmlspecialchars($old['full_name']) ?>" maxlength="100" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-at"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Choose a username" value="<?= htmlspecialchars($old['username']) ?>" minlength="4" maxlength="30" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($old['email']) ?>" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control" placeholder="At least 8 characters" minlength="8" required>
            <button type="button" class="btn-eye" data-target="password"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
            <input type="password" name="confirm" id="confirm" class="form-control" placeholder="Re-enter password" required>
            <button type="button" class="btn-eye" data-target="confirm"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button class="btn btn-success btn-auth"><i class="bi bi-person-check me-1"></i>Create Account</button>
      </form>

      <div class="auth-foot">Already have an account? <a href="login.php">Log in</a></div>
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
document.getElementById('regForm').addEventListener('submit', function (e) {
  if (document.getElementById('password').value !== document.getElementById('confirm').value) {
    e.preventDefault();
    alert('Passwords do not match.');
  }
});
</script>
<?php include 'cat.php'; ?>
</body>
</html>