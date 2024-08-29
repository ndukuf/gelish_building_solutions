<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


if(isset($_POST['submitContact']))
{
    $fullname = $_POST['full_name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;//Enable verbose debug output
        //Send using SMTP
        $mail->isSMTP();
        //Enable SMTP authentication
        $mail->SMTPAuth = true;
        
        //Set the SMTP server to send through
        $mail->Host = 'smtp.gmail.com';
        //SMTP username
        $mail->Username = 'victormunandi4@gmail.com';
        //SMTP password
        $mail->Password = 'amfvouvtnkvgrbio';

        //ENCRYPTION_SMTPS 465 - Enable implicit TLS encryption
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('victormunandi4@gmail.com', 'Victor Munandi Tech');
        //Add a recipient
        $mail->addAddress('victormunandi4@gmail.com', 'Victor Munandi Tech');

        //Content
        //Set email format to HTML
        $mail->isHTML(true);
        $mail->Subject = 'New enquiry - Victor Munandi Tech Contact Form';

        $bodyContent = '<div>Hello, you got a new enquiry</div>
        <div>Fullname: '.$fullname.'</div>
        <div>Email: '.$email.'</div>
        <div>Subject: '.$subject.'</div>
        <div>Message: '.$message.'</div>';

        $mail->Body = $bodyContent; 
        
        if($mail->send()) {
            $_SESSION['status'] = "Thank you contact us - Team Victor Munandi Tech";
            header("Location: {$_SERVER["HTTP_REFERER"]}");
            exit(0);
        } else {
            $_SESSION['status'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            header("Location: {$_SERVER["HTTP_REFERER"]}");
            exit(0);
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else{
    header('Location: index.php');
    exit();
}
?>