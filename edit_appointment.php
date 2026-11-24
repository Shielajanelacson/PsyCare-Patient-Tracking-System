<?php
include 'db.php';
$id = (int)$_GET['id'];
$error = '';

$stmt = $conn->prepare("SELECT a.*, p.full_name FROM appointments a JOIN patients p ON a.patient_id=p.id WHERE a.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$a = $stmt->get_result()->fetch_assoc();
if (!$a) { die("Appointment not found."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taken = 0;
    if ($_POST['status'] === 'Scheduled') {
        $chk = $conn->prepare("SELECT COUNT(*) c FROM appointments WHERE room=? AND appt_date=? AND appt_time=? AND status='Scheduled' AND id<>?");
        $chk->bind_param("sssi", $_POST['room'], $_POST['appt_date'], $_POST['appt_time'], $id);
        $chk->execute();
        $taken = $chk->get_result()->fetch_assoc()['c'];
    }

    if ($taken > 0) {
        $error = $_POST['room'] . " is already booked at that date and time. Please choose another room or time.";
    } else {
        $stmt = $conn->prepare("UPDATE appointments SET appt_date=?, appt_time=?, purpose=?, room=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", $_POST['appt_date'], $_POST['appt_time'], $_POST['purpose'], $_POST['room'], $_POST['status'], $id);
        $stmt->execute();

        $labels = ['appt_date' => 'Date', 'appt_time' => 'Time', 'room' => 'Room', 'purpose' => 'Purpose', 'status' => 'Status'];
        $changes = [];
        foreach ($labels as $field => $label) {
            $old = ($field === 'appt_time') ? substr($a[$field], 0, 5) : (string)$a[$field];
            $new = ($field === 'appt_time') ? substr($_POST[$field], 0, 5) : (string)$_POST[$field];
            if ($old !== $new) {
                $changes[] = $label . ': ' . $old . ' to ' . $new;
            }
        }

        if ($changes) {
            $type = 'Appointment Updated';
            if ($_POST['status'] === 'Completed' && $a['status'] !== 'Completed') {
                $type = 'Appointment Completed';
            } elseif ($_POST['status'] === 'Cancelled' && $a['status'] !== 'Cancelled') {
                $type = 'Appointment Cancelled';
            }
            logHistory($conn, $type, $a['full_name'], implode(' | ', $changes));
        }

        header("Location: index.php");
        exit;
    }
}
include 'header.php';
?>
<h3>Update Appointment: <?= htmlspecialchars($a['full_name']) ?></h3>
<?php if ($error): ?>
  <div class="alert alert-danger mt-3"><i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="POST" class="card card-body mt-3" onsubmit="return confirm('Save these changes?')">
  <div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Date</label>
      <input type="date" name="appt_date" class="form-control" value="<?= $a['appt_date'] ?>" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Time</label>
      <input type="time" name="appt_time" class="form-control" value="<?= $a['appt_time'] ?>" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Room Assignment</label>
      <select name="room" class="form-select">
        <?php foreach ($rooms as $r): ?>
          <option <?= $a['room'] === $r ? 'selected' : '' ?>><?= $r ?></option>
        <?php endforeach; ?>
      </select></div>
  </div>
  <div class="mb-3"><label class="form-label">Purpose</label>
    <input type="text" name="purpose" class="form-control" value="<?= htmlspecialchars($a['purpose']) ?>"></div>
  <div class="mb-3"><label class="form-label">Status</label>
    <select name="status" class="form-select">
      <?php foreach (['Scheduled','Completed','Cancelled'] as $s): ?>
        <option <?= $a['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select></div>
  <div>
    <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Appointment</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<?php include 'footer.php'; ?>