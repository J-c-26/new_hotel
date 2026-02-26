<?php
include 'db.php';

// Auto-cancel logic 
$conn->query("UPDATE bookings SET status = 'Cancelled' WHERE status = 'Pending' AND created_at < NOW() - INTERVAL 1 DAY");

// Get History 
$histQ = $conn->query("SELECT room_name, status, price, created_at FROM bookings ORDER BY id DESC LIMIT 5");
$history = [];
while($row = $histQ->fetch_assoc()) { 
    $history[] = $row; 
}

// Get the one current active booking
$res = $conn->query("SELECT status, room_name FROM bookings WHERE checked_out = 0 ORDER BY id DESC LIMIT 1");
$row = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode([
    "currentStatus" => $row ? $row['status'] : "No Reservation",
    "roomName" => $row ? $row['room_name'] : "",
    "history" => $history
]);
?>