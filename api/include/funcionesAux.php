<?php

/**
 * Clase para funciones auxiliares, envío de email's
 * 27/01/2022 V1.0
 */

include_once("include/phpmailer/class.phpmailer.php");
define("WS_UID", $_SESSION['empresa_GLS_UID']);

class funcionesAux
{
    function __construct()
    {
        $this->time = time();
        $this->empresa = $_SESSION["empresa"];
        $this->empresa_GLS_UID = $_SESSION["empresa_GLS_UID"];
        $this->codcli = $_SESSION["codcli"];

        $this->email_host     = 'mail.envialog.com';
        $this->email_port     = '25';
        $this->email_from     = 'informacion@envialog.com';
        $this->email_from_name  = 'Gestor intranet ' . $_SESSION['empresa_nombre'];
        $this->email_username   = 'informacion@envialog.com';
        $this->email_password   = 'CRM_crm_';
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

$funcionesAux = new funcionesAux;
