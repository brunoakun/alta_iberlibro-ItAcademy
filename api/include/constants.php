<?
/**
 * Constants.php
 *
 * Fichero de configuración
 * Constantes definidas en la aplicación
 * Formato MAYUSCULAS espacios marcados como "_"
 *
 */
 
define("CSV_SEPARADOR", ";");


/**
 * Parámetros de conexión al
 * Servidor MySQL. 
 */
include("connex.php");	// acceso a BBDD

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS",         "usuaris");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");
define("TBL_LOG",           "log_envios");


/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("GUEST_NAME", " ");

define("SA_LEVEL", 9);
define("ADMIN_LEVEL", 7);
define("USER_LEVEL",  3);
define("USER_MANIFIESTO",  1);
define("GUEST_LEVEL", 0);

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 60);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */

define("SET_URL", ""); // Url pública
//define("EMAIL_SEND", "antonionavarro@ventas.envialog.com"); // email donde enviar el formulario de solicitud  de alta
define("EMAIL_SEND", "bruno.akun@gmail.com"); // email donde enviar el formulario de solicitud  de alta
define("EMAIL_FROM_CONTACT", "informacion@envialog.com"); // email remitente del formulario de contacto	
define("EMAIL_FROM_NAME", "Soporte Gestión Envíos APP"); // nombre del remitente 
define("EMAIL_FROM_ADDR", "informacion@envialog.com"); // email del remitente 
define("EMAIL_WELCOME", false); // Enviar eMail de bienvenida al registrarse
define("SET_PASS_URL", SET_URL."/usuario-set-password.php");	// Url recuperación del password


/**
 * eMail server & credenciales 
 */

define("EMAIL_HOST",        "mail.envialog.com");                      // $mail->Host 
define("EMAIL_USERNAME",    "xxx@envialog.com");               // $mail->Username
define("EMAIL_PASSWORD",    "xxx");                               // $mail->Password
define("EMAIL_SMTPSECURE",  "ssl");  	                              // $mail->SMTPSecure
define("EMAIL_PORT",        "25");  	                              // $mail->Port


/**
 * Gestión de la tabla LOG
 * Registrar o No actividad para cada proceso
 */
define("LOG_USR_LOGIN",  false);        // LogIn, LogOut, Tiempo sin actividad excedido
define("LOG_USR",  		 true);			// Manenimiento usuarios, cambios de contraseña
define("LOG_SIN_NIVEL",  true);			// Intentos de acceso sin nivel
define("LOG_ACIVIDAD",   true);			// Mantenimiento de datos
define("LOG_EXPORT",     true);			// Exportaciones de datos
define("LOG_ERR_CRON",   true);         // Registrar ERRORES de procesos CRON

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);

?>