<?php
include 'db.php';
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM patients WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
if (!$p) { die("Patient not found."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE patients SET full_name=?, age=?, contact=?, disorder=?, status=?, notes=? WHERE id=?");
    $stmt->bind_param("sissssi", $_POST['full_name'], $_POST['age'], $_POST['contact'], $_POST['disorder'], $_POST['status'], $_POST['notes'], $id);
    $stmt->execute();

    $labels = ['full_name' => 'Name', 'age' => 'Age', 'contact' => 'Contact', 'disorder' => 'Disorder', 'status' => 'Status', 'notes' => 'Notes'];
    $changes = [];
    foreach ($labels as $field => $label) {
        if ((string)$p[$field] !== (string)$_POST[$field]) {
            $changes[] = ($field === 'notes') ? 'Progress notes updated' : $label . ': ' . $p[$field] . ' to ' . $_POST[$field];
        }
    }

    if ($_POST['status'] === 'Discharged' && $p['status'] !== 'Discharged') {
        logHistory($conn, 'Patient Discharged', $_POST['full_name'], 'Marked as done. ' . implode(' | ', $changes));
    } elseif ($changes) {
        logHistory($conn, 'Patient Updated', $_POST['full_name'], implode(' | ', $changes));
    }

    header("Location: index.php");
    exit;
}
include 'header.php';
?>
<h3>Update Patient</h3>
<form method="POST" class="card card-body mt-3" onsubmit="return confirm('Save these changes?')">
  <div class="mb-3"><label class="form-label">Full Name</label>
    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($p['full_name']) ?>" required></div>
  <div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Age</label>
      <input type="number" name="age" class="form-control" value="<?= $p['age'] ?>" required></div>
    <div class="col-md-8 mb-3"><label class="form-label">Contact</label>
      <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($p['contact']) ?>"></div>
  </div>
  <div class="mb-3"><label class="form-label">Disorder / Diagnosis</label>
    <input type="text" name="disorder" class="form-control" value="<?= htmlspecialchars($p['disorder']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Status</label>
    <select name="status" class="form-select">
      <?php foreach (['Active','Improving','Stable','Discharged'] as $s): ?>
        <option <?= $p['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select></div>
  <div class="mb-3"><label class="form-label">Progress Notes</label>
    <textarea name="notes" class="form-control" rows="4"><?= htmlspecialchars($p['notes']) ?></textarea></div>
  <div>
    <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Patient</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<?php include 'footer.php'; ?>