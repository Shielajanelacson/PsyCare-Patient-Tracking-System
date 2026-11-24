<?php
include 'db.php';
include 'header.php';

$totalPatients = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$upcoming = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='Scheduled' AND appt_date >= CURDATE()")->fetch_assoc()['c'];
$activeCases = $conn->query("SELECT COUNT(*) c FROM patients WHERE status='Active'")->fetch_assoc()['c'];
$cancelled = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='Cancelled'")->fetch_assoc()['c'];

$appts = $conn->query("SELECT a.*, p.full_name FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        ORDER BY a.appt_date, a.appt_time");
$patients = $conn->query("SELECT * FROM patients ORDER BY id");
$recentNotes = $conn->query("SELECT n.*, p.full_name FROM notes n
        JOIN patients p ON n.patient_id = p.id
        ORDER BY n.updated_at DESC LIMIT 3");

$patientBadge = ['Active' => 'blue', 'Improving' => 'green', 'Stable' => 'brown', 'Discharged' => 'gray'];
$apptBadge    = ['Scheduled' => 'blue', 'Completed' => 'green', 'Cancelled' => 'red'];
?>

<div class="hero mb-4">
  <div class="row align-items-center g-4">
    <div class="col-lg-6 text-center text-lg-start">
      <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
      <div class="accent-line mx-auto mx-lg-0"></div>
      <p id="quote" class="quote-text mx-auto mx-lg-0"></p>
      <div id="author" class="quote-author"></div>
    </div>
    <div class="col-lg-6">
      <?php include 'brain.php'; ?>
    </div>
  </div>
</div>

<div class="row g-3 mb-2">
  <div class="col-6 col-lg-3"><div class="card stat stat-blue"><div class="card-body">
    <div class="num"><?= $totalPatients ?></div><div class="label">Total Patients</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat stat-green"><div class="card-body">
    <div class="num"><?= $upcoming ?></div><div class="label">Upcoming Appointments</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat stat-brown"><div class="card-body">
    <div class="num"><?= $activeCases ?></div><div class="label">Active Cases</div></div></div></div>
  <div class="col-6 col-lg-3"><div class="card stat stat-red"><div class="card-body">
    <div class="num"><?= $cancelled ?></div><div class="label">Cancelled Sessions</div></div></div></div>
</div>

<h4 class="section-title"><i class="bi bi-calendar3 me-2"></i>Appointment Schedule and Room Assignment</h4>
<div class="table-wrap table-responsive">
  <table class="table table-clinic">
    <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Room</th><th>Purpose</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($a = $appts->fetch_assoc()): ?>
      <tr>
        <td class="fw-semibold"><?= htmlspecialchars($a['full_name']) ?></td>
        <td><?= date("M d, Y", strtotime($a['appt_date'])) ?></td>
        <td><?= date("g:i A", strtotime($a['appt_time'])) ?></td>
        <td><span class="badge-soft badge-gray"><i class="bi bi-door-open me-1"></i><?= htmlspecialchars($a['room']) ?></span></td>
        <td><?= htmlspecialchars($a['purpose']) ?></td>
        <td><span class="badge-soft badge-<?= $apptBadge[$a['status']] ?>"><?= $a['status'] ?></span></td>
        <td><a class="btn btn-sm btn-warning" href="edit_appointment.php?id=<?= $a['id'] ?>"><i class="bi bi-pencil-square me-1"></i>Update</a></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<h4 class="section-title"><i class="bi bi-people me-2"></i>Patients and Disorder Updates</h4>
<div class="table-wrap table-responsive">
  <table class="table table-clinic">
    <thead><tr><th>Name</th><th>Age</th><th>Contact</th><th>Disorder</th><th>Status</th><th>Progress Notes</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($p = $patients->fetch_assoc()): ?>
      <tr>
        <td class="fw-semibold"><?= htmlspecialchars($p['full_name']) ?></td>
        <td><?= $p['age'] ?></td>
        <td><?= htmlspecialchars($p['contact']) ?></td>
        <td><?= htmlspecialchars($p['disorder']) ?></td>
        <td><span class="badge-soft badge-<?= $patientBadge[$p['status']] ?>"><?= $p['status'] ?></span></td>
        <td><?= htmlspecialchars($p['notes']) ?></td>
          <td class="text-nowrap">
          <a class="btn btn-sm btn-warning" href="edit_patient.php?id=<?= $p['id'] ?>"><i class="bi bi-pencil-square me-1"></i>Update</a>
          <form method="POST" action="delete_patient.php" class="d-inline" onsubmit="return confirm('Remove this patient? Their appointments and notes will also be deleted.')">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <button class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Remove</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<div class="d-flex justify-content-between align-items-center">
  <h4 class="section-title"><i class="bi bi-journal-text me-2"></i>Recent Notes</h4>
  <a href="notes.php" class="btn btn-sm btn-primary">View All Notes</a>
</div>
<div class="row g-3">
  <?php while ($n = $recentNotes->fetch_assoc()): ?>
    <div class="col-md-4">
      <div class="card note-card h-100"><div class="card-body">
        <div class="note-patient"><?= htmlspecialchars($n['full_name']) ?></div>
        <h6 class="mb-2"><?= htmlspecialchars($n['title']) ?></h6>
        <p class="mb-2"><?= nl2br(htmlspecialchars($n['note_text'])) ?></p>
        <div class="note-meta"><?= date("M d, Y g:i A", strtotime($n['updated_at'])) ?></div>
      </div></div>
    </div>
  <?php endwhile; ?>
</div>

<script>
const quotes = [
  { q: "When we are no longer able to change a situation, we are challenged to change ourselves.", a: "Viktor Frankl" },
  { q: "The privilege of a lifetime is to become who you truly are.", a: "Carl Jung" },
  { q: "The curious paradox is that when I accept myself just as I am, then I can change.", a: "Carl Rogers" }
];
const pick = quotes[Math.floor(Math.random() * quotes.length)];
document.getElementById("quote").textContent = "\u201C" + pick.q + "\u201D";
document.getElementById("author").textContent = "\u2014 " + pick.a;
</script>

<?php include 'footer.php'; ?>