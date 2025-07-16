<?php
require 'config/database.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM birthdays WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header('Location: view_birthdays.php');
exit; 