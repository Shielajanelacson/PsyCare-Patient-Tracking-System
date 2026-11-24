<?php
include 'db.php';
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM notes WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$n = $stmt->get_result()->fetch_assoc();
if (!$n) { die("Note not found."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE notes SET patient_id=?, title=?, note_text=? WHERE id=?");
    $stmt->bind_param("issi", $_POST['patient_id'], $_POST['title'], $_POST['note_text'], $id);
    $stmt->execute();

    $changed = ((int)$n['patient_id'] !== (int)$_POST['patient_id'])
        || $n['title'] !== $_POST['title']
        || $n['note_text'] !== $_POST['note_text'];
    if ($changed) {
        logHistory($conn, 'Note Updated', patientName($conn, (int)$_POST['patient_id']), 'Title: ' . $_POST['title']);
    }

    header("Location: notes.php");
    exit;
}
$patients = $conn->query("SELECT id, full_name FROM patients ORDER BY full_name");
include 'header.php';
?>
<h3>Update Note</h3>
<form method="POST" class="card card-body mt-3" onsubmit="return confirm('Save these changes?')">
  <div class="mb-3"><label class="form-label">Patient</label>
    <select name="patient_id" class="form-select" required>
      <?php while ($p = $patients->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>" <?= $n['patient_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['full_name']) ?></option>
      <?php endwhile; ?>
    </select></div>
  <div class="mb-3"><label class="form-label">Title</label>
    <input type="text" name="title" class="form-control" maxlength="120" value="<?= htmlspecialchars($n['title']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Note</label>
    <textarea name="note_text" class="form-control" rows="6" required><?= htmlspecialchars($n['note_text']) ?></textarea></div>
  <div>
    <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Note</button>
    <a href="notes.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<?php include 'footer.php'; ?>