<? 
/**
 * Mailer.php
 *
 * The Mailer class is meant to simplify the task of sending
 * emails to users. Note: this email system will not work
 * if your server is not setup to send mail.
 *
 * If you are running Windows and want a mail server, check
 * out this website to see a list of freeware programs:
 * <http://www.snapfiles.com/freeware/server/fwmailserver.html>
 *
 */
 
class Mailer
{
   /**
    * sendWelcome - Sends a welcome message to the newly
    * registered user, also supplying the username and
    * password.
    */
   function sendWelcome($user, $email, $uid){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = EMAIL_FROM_NAME." - Bienvenido!";
      $body = $user.",\n\n"
             ."Se ha creado una solicitud de acceso al Proyecto de Atenuación Obras Centro Comercial Les Glories "
             ."Acceda para poder finalizar el proceso de alta y acceder al sistema\n"
             .SET_PASS_URL."?uid=".$uid."\n\n"
             .EMAIL_FROM_NAME." - ".EMAIL_FROM_NAME;
             //."con la siguiente informaci\u00f3n:\n\n";
             //."Usuario: ".$user."\n\n"
             //."Acceda para poder finalizar el proceso de alta y acceder al sistema:"
             //.SET_PASS_URL."\n\n"
             //.EMAIL_FROM_NAME." - ".EMAIL_FROM_NAME;

       //echo $body;
       //exit();

      return mail($email ,$subject,$body,$from);
   }
   
   /**
    * sendNewPass - Sends the newly generated password
    * to the user's email address that was specified at
    * sign-up.
    */
   function sendNewPass($user, $email, $pass){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "Gestio de Marquesines - Recordar usuari";
      $body = $user.",\n\n"
             ."Hem generat una nova paraula de pas segons el teu "
             ."requeriment, pots utilitzar aquesta paraula de pas "
             ."conjuntament amb el teu nom d'usuari per identificar-te a la web.\n\n"
             ."Usuari: ".$user."\n"
             ."Nova paraula de pas: ".$pass."\n\n"
             ."Es rocamanable que canviis aquesta paraula de pas "
             ."per una que et sigui m�s f�cil de recordar, ho pots "
             ."fer accedint al men� Gesti� Usuari un cop accedeixis. \n\n"
             ."Gestio de Marquesines - Powered by EngageIT.";
             
      return mail($email,$subject,$body,$from);
   }
   
   /**
    * sendCode - Sends the newly generated password
    * to the user's email address that was specified at
    * sign-up.
    */
   function sendCode($user, $email, $code){
	  $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "Autoprotec - Sol�licitar acc�s";
      $body = $user.",\n\n"
             ."A continuaci� es mostra un enlla� per modificar la vostre paraula de pas en cas que no la recordeu.\n\n"
             ."Feu clic al seg�ent enlla� per informar una nova paraula de pas i accedir:\n"
			 .SET_PASS_URL."?code=".$code
             ."\n ".EMAIL_FROM_NAME ;
             
      return mail($email,$subject,$body,$from);
   }
};

/* Initialize mailer object */
$mailer = new Mailer;
 
?>
