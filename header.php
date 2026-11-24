<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PsyCare - Patient Tracker</title>
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
<nav class="navbar navbar-expand-lg navbar-psy navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-heart-pulse-fill me-2"></i>PsyCare</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a>
        <a class="nav-link" href="add_patient.php"><i class="bi bi-person-plus me-1"></i>Patient</a>
        <a class="nav-link" href="add_appointment.php"><i class="bi bi-calendar-plus me-1"></i>Appointment</a>
        <a class="nav-link" href="notes.php"><i class="bi bi-journal-text me-1"></i>Notes</a>
        <a class="nav-link" href="history.php"><i class="bi bi-clock-history me-1"></i>History</a>
        <span class="navbar-text text-white-50 ms-lg-2"><i class="bi bi-person-badge me-1"></i><?= htmlspecialchars($_SESSION['full_name']) ?></span>
        <button id="themeToggle" class="btn btn-theme btn-sm px-3" type="button" title="Switch light/dark mode">
          <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
        </button>
        <a class="btn btn-theme btn-sm px-3" href="logout.php" onclick="return confirm('Log out of PsyCare?')"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
      </div>
    </div>
  </div>
</nav>
<div class="container py-4">