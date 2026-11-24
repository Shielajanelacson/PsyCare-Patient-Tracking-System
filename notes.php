<?php
include 'db.php';
include 'header.php';

$notes = $conn->query("SELECT n.*, p.full_name FROM notes n
        JOIN patients p ON n.patient_id = p.id
        ORDER BY n.updated_at DESC");
?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
  <h3 class="mb-0"><i class="bi bi-journal-text me-2"></i><?= htmlspecialchars($_SESSION['full_name']) ?>'s Notes
  <a href="add_note.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Add Note</a>
</div>

<div class="row g-3 mt-2">
  <?php while ($n = $notes->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card note-card h-100"><div class="card-body d-flex flex-column">
        <div class="note-patient"><?= htmlspecialchars($n['full_name']) ?></div>
        <h6 class="mb-2"><?= htmlspecialchars($n['title']) ?></h6>
        <p class="flex-grow-1"><?= nl2br(htmlspecialchars($n['note_text'])) ?></p>
        <div class="d-flex justify-content-between align-items-center">
          <span class="note-meta"><?= date("M d, Y g:i A", strtotime($n['updated_at'])) ?></span>
          <a href="edit_note.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square me-1"></i>Update</a>
        </div>
      </div></div>
    </div>
  <?php endwhile; ?>
</div>
<?php include 'footer.php'; ?>