<?php
include 'db.php';
include 'header.php';

$rows = $conn->query("SELECT * FROM history ORDER BY id DESC LIMIT 200");

$badge = [
  'Patient Added' => 'green',
  'Patient Updated' => 'blue',
  'Patient Discharged' => 'brown',
  'Patient Removed' => 'red',
  'Appointment Scheduled' => 'blue',
  'Appointment Updated' => 'gray',
  'Appointment Completed' => 'green',
  'Appointment Cancelled' => 'red',
  'Note Added' => 'green',
  'Note Updated' => 'brown'
];
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i>Activity History</h3>
  <input type="text" id="search" class="form-control" style="max-width:280px;" placeholder="Search history...">
</div>

<div class="table-wrap table-responsive mt-3">
  <table class="table table-clinic" id="historyTable">
    <thead><tr><th>Date and Time</th><th>Action</th><th>Patient</th><th>Details</th></tr></thead>
    <tbody>
    <?php if ($rows->num_rows === 0): ?>
      <tr><td colspan="4" class="text-center text-muted py-4">No activity yet.</td></tr>
    <?php endif; ?>
    <?php while ($h = $rows->fetch_assoc()): ?>
      <tr>
        <td class="text-nowrap"><?= date("M d, Y g:i A", strtotime($h['created_at'])) ?></td>
        <td><span class="badge-soft badge-<?= $badge[$h['action_type']] ?? 'gray' ?>"><?= htmlspecialchars($h['action_type']) ?></span></td>
        <td class="fw-semibold"><?= htmlspecialchars($h['patient_name']) ?></td>
        <td><?= htmlspecialchars($h['details']) ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
document.getElementById("search").addEventListener("input", function () {
  const term = this.value.toLowerCase();
  document.querySelectorAll("#historyTable tbody tr").forEach(function (row) {
    row.style.display = row.textContent.toLowerCase().includes(term) ? "" : "none";
  });
});
</script>
<?php include 'footer.php'; ?>