<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    $stmt = $conn->prepare("SELECT full_name, disorder FROM patients WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    if ($p) {
        $c1 = $conn->prepare("SELECT COUNT(*) c FROM appointments WHERE patient_id=?");
        $c1->bind_param("i", $id);
        $c1->execute();
        $apptCount = $c1->get_result()->fetch_assoc()['c'];

        $c2 = $conn->prepare("SELECT COUNT(*) c FROM notes WHERE patient_id=?");
        $c2->bind_param("i", $id);
        $c2->execute();
        $noteCount = $c2->get_result()->fetch_assoc()['c'];

        logHistory($conn, 'Patient Removed', $p['full_name'],
            'Disorder: ' . $p['disorder'] . ' | Deleted ' . $apptCount . ' appointment(s) and ' . $noteCount . ' note(s)');

        $del = $conn->prepare("DELETE FROM patients WHERE id=?");
        $del->bind_param("i", $id);
        $del->execute();
    }
}
header("Location: index.php");
exit;
?>