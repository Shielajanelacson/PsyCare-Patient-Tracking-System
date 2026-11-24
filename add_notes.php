<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO notes (patient_id, title, note_text) VALUES (?,?,?)");
    $stmt->bind_param("iss", $_POST['patient_id'], $_POST['title'], $_POST['note_text']);
    $stmt->execute();
    logHistory($conn, 'Note Added', patientName($conn, (int)$_POST['patient_id']), 'Title: ' . $_POST['title']);
    header("Location: notes.php");
    exit;
}
$patients = $conn->query("SELECT id, full_name FROM patients ORDER BY full_name");
include 'header.php';
?>
<h3>Add Note</h3>
<form method="POST" class="card card-body mt-3">
  <div class="mb-3"><label class="form-label">Patient</label>
    <select name="patient_id" class="form-select" required>
      <option value="">Select patient</option>
      <?php while ($p = $patients->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
      <?php endwhile; ?>
    </select></div>
  <div class="mb-3"><label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" maxlength="120" required></div>
  <div class="mb-3"><label class="form-label">Note</label>
    <textarea name="note_text" class="form-control" rows="6" required></textarea></div>
  <div>
    <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Save Note</button>
    <a href="notes.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<?php include 'footer.php'; ?>