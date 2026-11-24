<?php
include 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chk = $conn->prepare("SELECT COUNT(*) c FROM appointments WHERE room=? AND appt_date=? AND appt_time=? AND status='Scheduled'");
    $chk->bind_param("sss", $_POST['room'], $_POST['appt_date'], $_POST['appt_time']);
    $chk->execute();
    $taken = $chk->get_result()->fetch_assoc()['c'];

    if ($taken > 0) {
        $error = $_POST['room'] . " is already booked at that date and time. Please choose another room or time.";
    } else {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, appt_date, appt_time, purpose, room) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $_POST['patient_id'], $_POST['appt_date'], $_POST['appt_time'], $_POST['purpose'], $_POST['room']);
        $stmt->execute();
        logHistory($conn, 'Appointment Scheduled', patientName($conn, (int)$_POST['patient_id']),
            $_POST['appt_date'] . ' ' . substr($_POST['appt_time'], 0, 5) . ' | ' . $_POST['room'] . ' | ' . $_POST['purpose']);
        header("Location: index.php");
        exit;
    }
}
$patients = $conn->query("SELECT id, full_name FROM patients ORDER BY full_name");
include 'header.php';
?>
<h3>Schedule Appointment</h3>
<?php if ($error): ?>
  <div class="alert alert-danger mt-3"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="POST" class="card card-body mt-3">
  <div class="mb-3"><label class="form-label">Patient</label>
    <select name="patient_id" class="form-select" required>
      <option value="">Select patient</option>
      <?php while ($p = $patients->fetch_assoc()): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
      <?php endwhile; ?>
    </select></div>
  <div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Date</label>
      <input type="date" name="appt_date" class="form-control" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Time</label>
      <input type="time" name="appt_time" class="form-control" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Room Assignment</label>
      <select name="room" class="form-select" required>
        <?php foreach ($rooms as $r): ?>
          <option><?= $r ?></option>
        <?php endforeach; ?>
      </select></div>
  </div>
  <div class="mb-3"><label class="form-label">Purpose</label>
    <input type="text" name="purpose" class="form-control"></div>
  <div>
    <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Save Appointment</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<?php include 'footer.php'; ?>