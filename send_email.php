<?php
#Этот файл отправляет приветсвенное Email новым пользователям. Подключается в момент регистрации

require_once 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
$mail->SMTPDebug = 2;

$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug = 0;

#Настройки почты взяты с VDS
$mail->Host = 'ssl://smtp.timeweb.ru';
$mail->Port = 465;
$mail->Username = 'hello@ref.video';
$mail->Password = '';

$mail->setFrom('hello@ref.video', 'Video Reference Portal');
$mail->AddReplyTo('hello@ref.video', 'Video Reference Portal');

#Отправляем новому пользователю, email прилетит из основного файла
$mail->addAddress($email_to_send, '');

$subject = 'Hello and Welcome to REF.Video';
$mail->Subject = $subject;

$mail->AddCustomHeader( "X-Confirm-Reading-To: who_read_email@ref.video" );
$mail->AddCustomHeader( "Return-receipt-to: who_read_email@ref.video" );
$mail->AddCustomHeader( "Disposition-Notification-To: who_read_email@ref.video" );
$mail->ConfirmReadingTo = 'who_read_email@ref.video';

#Тело HTML письма находится в папке php_library, подготовленно для двух разных языков
$body = file_get_contents("php_library/email_temp.php");;
$mail->msgHTML($body);

$mail->send();

?>
