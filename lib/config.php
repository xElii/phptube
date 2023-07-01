<?php
// Db connection
define('DB_SERVER', 'DB_HOST');
define('DB_USERNAME', 'test');
define('DB_PASSWORD', 'DB_Password');
define('DB_NAME', 'test');
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Sendmail function
function sendmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.sendgrid.net';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'apikey';
    $mail->Password   = 'yourkey';
    $mail->Port       = 465;
    $mail->SMTPSecure = 'ssl';
    $mail->setFrom('mail@yourdomain.tld', 'Datatube');
    $mail->addAddress($to);
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->send();
}
?>