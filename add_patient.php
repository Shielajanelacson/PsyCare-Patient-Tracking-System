<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO patients (full_name, age, contact, disorder, status, notes) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("sissss", $_POST['full_name'], $_POST['age'], $_POST['contact'], $_POST['disorder'], $_POST['status'], $_POST['notes']);
    $stmt->execute();
    logHistory($conn, 'Patient Added', $_POST['full_name'], 'Disorder: ' . $_POST['disorder'] . ' | Status: ' . $_POST['status']);
    header("Location: index.php");
    exit;
}
include 'header.php';
?>
<h3>Add New Patient</h3>
<form method="POST" class="card card-body mt-3" onsubmit="return validateForm()">
  <div class="mb-3"><label class="form-label">Full Name</label>
    <input type="text" name="full_name" id="full_name" class="form-control" required></div>
  <div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Age</label>
      <input type="number" name="age" id="age" class="form-control" min="1" max="120" required></div>
    <div class="col-md-8 mb-3"><label class="form-label">Contact</label>
      <input type="text" name="contact" class="form-control"></div>
  </div>
  <div class="mb-3"><label class="form-label">Disorder / Diagnosis</label>
    <input type="text" name="disorder" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Status</label>
    <select name="status" class="form-select">
      <option>Active</option><option>Improving</option><option>Stable</option><option>Discharged</option>
    </select></div>
  <div class="mb-3"><label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3"></textarea></div>
  <div>
    <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Save Patient</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<script>
function validateForm() {
  const age = parseInt(document.getElementById("age").value);
  if (age < 1 || age > 120) { alert("Please enter a valid age."); return false; }
  return true;
}
</script>
<?php include 'footer.php'; ?>