</div>
<div class="site-footer">
  <i class="bi bi-shield-lock me-1"></i>PsyCare &middot; Confidential patient information &middot; For authorized practitioners only
</div>
<?php include 'cat.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const root = document.documentElement;
  const icon = document.getElementById('themeIcon');
  function syncIcon() {
    icon.className = root.getAttribute('data-bs-theme') === 'dark'
      ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
  }
  syncIcon();
  document.getElementById('themeToggle').addEventListener('click', function () {
    const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-bs-theme', next);
    try { localStorage.setItem('psycare-theme', next); } catch (e) {}
    syncIcon();
  });
</script>
</body>
</html>