<?php

/**
 * Clase para envío de email's
 * 29/07/2022 V1.0
 */

include_once("include/phpmailer/class.phpmailer.php");

class EnviarEmail
{
    function __construct()
    {
        $this->time = time();

        $this->email_host     = EMAIL_HOST;
        $this->email_port     = EMAIL_PORT;
        $this->email_from     = EMAIL_FROM_ADDR;
        $this->email_from_name  = EMAIL_FROM_NAME;
        $this->email_username   = EMAIL_USERNAME;
        $this->email_password   = EMAIL_PASSWORD;
    }


    /*******************************
     * sendEmail (destinatario, asunto, mensaje)
     * Envía eMail
     */
    function sendEmail($destinatario, $asunto, $mensaje)
    {
        // Parámetros del servidor de correo
        $mail = new PHPMailer();

        $mail->Mailer   = "smtp";
        $mail->Host   = $this->email_host;
        $mail->Port   =  $this->email_port;
        $mail->SMTPAuth = true;                     // turn on SMTP authentication
        $mail->Username = $this->email_username;    // SMTP username
        $mail->Password = $this->email_password;    // SMTP password  

        //Body
        $body = $mensaje;
        $body .= "<br><hr/><i>No contestes a este eMail, se ha generado automaticamente desde " . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . " </i>";

        //headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $mail->isHTML(true);
        $asunto = "=?UTF-8?B?" . base64_encode($asunto) . "=?=";
        //  $mail->CharSet = "UTF-8";

        //Envío
        $mail->SetFrom($this->email_from, $this->email_from_name);
        $mail->AddReplyTo($this->email_from, $this->email_from_name);
        $mail->AltBody = '';
        $mail->Subject = utf8_decode($asunto);
        $mail->MsgHTML($body);
        $mail->AddAddress($destinatario);

        if (!$mail->Send()) {
            return ($mail->ErrorInfo);
        } else {
            return ("OK");
        }
    }
}

$enviarEmail = new EnviarEmail;
