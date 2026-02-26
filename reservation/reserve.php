<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = "carlobalderama9@gmail.com"; // change niyo nalang
    $token = bin2hex(random_bytes(16));
    

    $room = isset($_POST['room_name']) ? $conn->real_escape_string($_POST['room_name']) : "Luxury Suite";
    $price = isset($_POST['price']) ? $conn->real_escape_string($_POST['price']) : "$0";


    $sql = "INSERT INTO bookings (email, token, status, room_name, price) 
            VALUES ('$email', '$token', 'Pending', '$room', '$price')";
    
    if ($conn->query($sql) === TRUE) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'carlobalderama9@gmail.com'; // change niyo to sa email mo
            $mail->Password   = 'hlhx ifnf gdzk bdfn'; // panoorin niyo sa yt kung pano kumuha ng ganto sa google gamit PHPMailer
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('carlobalderama9@gmail.com', 'Serene Haven Resort');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "ACTION REQUIRED: Confirm Stay for $room";
            
            $confirm_url = "http://localhost/reservation/confirm.php?token=$token";
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px;'>
                    <h2 style='color: #b89550;'>Serene Haven Reservation</h2>
                    <p>Thank you for choosing us! Please confirm your booking details:</p>
                    <ul>
                        <li><b>Room:</b> $room</li>
                        <li><b>Price:</b> $price</li>
                    </ul>
                    <p>To finalize this reservation, click the button below:</p>
                    <a href='$confirm_url' style='background: #b89550; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Confirm Reservation</a>
                </div>";

            $mail->send();
            echo json_encode(["status" => "Pending"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "Error", "message" => "Mail failed: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["status" => "Error", "message" => $conn->error]);
    }
}
?>