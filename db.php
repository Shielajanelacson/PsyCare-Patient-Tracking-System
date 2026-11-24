<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "psycare_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$rooms = ['Room 101', 'Room 102', 'Room 103', 'Therapy Room A', 'Therapy Room B'];

function logHistory($conn, $type, $patient, $details = '') {
    $details = mb_substr($details, 0, 255);
    $stmt = $conn->prepare("INSERT INTO history (action_type, patient_name, details) VALUES (?,?,?)");
    $stmt->bind_param("sss", $type, $patient, $details);
    $stmt->execute();
}

function patientName($conn, $id) {
    $stmt = $conn->prepare("SELECT full_name FROM patients WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ? $row['full_name'] : 'Unknown patient';
}

if (!isset($publicPage) && empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>