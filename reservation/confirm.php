<?php
include 'db.php';

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    
    // Update status to Confirmed only if it's currently Pending
    $sql = "UPDATE bookings SET status = 'Confirmed' 
            WHERE token = '$token' AND status = 'Pending'";
    
    if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
        echo "
        <div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h1 style='color:green;'>Reservation Confirmed!</h1>
            <p>Your stay at Serene Haven is now officially booked.</p>
            <p>You can now return to the main site and refresh the page.</p>
            <button onclick='window.close()' style='padding:10px; cursor:pointer;'>Close This Window</button>
        </div>";
    } else {
        echo "
        <div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h1 style='color:red;'>Link Invalid or Expired</h1>
            <p>This reservation might have already been confirmed or the link is broken.</p>
        </div>";
    }
}
?>