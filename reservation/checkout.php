<?php
include 'db.php';
$conn->query("UPDATE bookings SET checked_out = 1, status = 'Completed' WHERE checked_out = 0");
echo "Success";
?>