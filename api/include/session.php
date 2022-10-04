<?
ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED );	
/**
 * Session.php
 *
 * Versión php 7.x
 * 
 * The Session class is meant to simplify the task of keeping
 * track of logged in users and also guests.
 *
 */

include("database.php");
include("mailer.php");
include("form.php");
include_once("encrypt.php");    // Encriptar/Desencriptar


class Session
{
   var $username;     //Username given on sign-up
   var $userid;       //Random value generated on current login
   var $userlevel;    //The level to which the user pertains
   var $time;         //Time user was last active (page loaded)
   var $logged_in;    //True if user is logged in, false otherwise
   var $userinfo = array();  //The array holding all user info
   var $url;          //The page url current being viewed
   var $referrer;     //Last recorded site page viewed
   var $webmobileid;  //User's webmovile, 0(all) for admin
   var $strwebmobile;
   var $demarcacion;  //User's demarcación, 0(all) for admin
   var $strdemarcacion;
   var $userfoto;     //User's foto

   /**
    * Note: referrer should really only be considered the actual
    * page referrer in process.php, any other time it may be
    * inaccurate.
    */

   /* Class constructor */
   //function Session(){
   function __construct()
   {
      $this->time = time();
      $this->startSession();
   }

   /**
    * startSession - Performs all the actions necessary to 
    * initialize this session object. Tries to determine if the
    * the user has logged in already, and sets the variables 
    * accordingly. Also takes advantage of this page load to
    * update the active visitors tables.
    */
   function startSession()
   {
      global $database;  //The database connection
      session_start();   //Tell PHP to start the session

      /* Determine if user is logged in */
      $this->logged_in = $this->checkLogin();

      /**
       * Set guest value to users not logged in, and update
       * active guests table accordingly.
       */
      if (!$this->logged_in) {
         $this->username = $_SESSION['username'] = GUEST_NAME;
         $this->userlevel = GUEST_LEVEL;
         $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
      } else {
         /* Controlar el tiempo de inactividad */
         if ($_SESSION['last_activity'] < time() - USER_TIMEOUT * 60) {
            $this->logout(true);
         } else {
            $_SESSION['last_activity'] = time();
         }
         /* Update users last active timestamp */
         $database->addActiveUser($this->username, $this->time);
      }

      /* Remove inactive visitors from database */
      $database->removeInactiveUsers();
      $database->removeInactiveGuests();

      /* Set referrer page */
      if (isset($_SESSION['url'])) {
         $this->referrer = $_SESSION['url'];
      } else {
         $this->referrer = "/";
      }

      /* Set current url */
      $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
   }

   function checkLogin()
   {
      global $database;  //The database connection
      /* Check if user has been remembered */
      if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
         $this->username = $_SESSION['username'] = my_simple_crypt($_COOKIE['cookname'], 'd');
         $this->userid   = $_SESSION['userid']   = $_COOKIE['cookid'];
      }
      //echo $_SESSION['username'];

      /* Username and userid have been set and not guest */
      if (
         isset($_SESSION['username']) && isset($_SESSION['userid']) &&
         $_SESSION['username'] != GUEST_NAME
      ) {
         /* Confirm that username and userid are valid */
         if ($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0) {
            /* Variables are incorrect, user not logged in */
            unset($_SESSION['username']);
            unset($_SESSION['userid']);
            return false;
         }

         /* User is logged in, set class variables */
         $this->userinfo     = $database->getUserInfo($_SESSION['username']);

         $this->username     = $this->userinfo['username'];
         $this->userid       = $this->userinfo['userid'];
         $this->userlevel   = $this->userinfo['userlevel'];
         $this->empresa      = $this->userinfo['empresa'];
         $this->departamento = $this->userinfo['departamento'];
         $this->pais         = $this->userinfo['pais'];
         $this->userfoto   = $this->userinfo['userfoto'];

         $this->empresa_GLS_UID = $this->userinfo['empresa_GLS_UID'];
         $this->codcli = $this->userinfo['codcli'];
         $this->empresa_logo = $this->userinfo['empresa_logo'];
         $this->empresa_logo_top = $this->userinfo['empresa_logo_top'];
         $this->empresa_logo_label = $this->userinfo['empresa_logo_label'];
         $this->empresa_nombre = $this->userinfo['empresa_nombre'];
         $this->empresa_email_agencia = $this->userinfo['empresa_email_agencia'];
         $this->UID_recogidas = $this->userinfo['UID_recogidas'];
         $this->cp_recogidas = $this->userinfo['cp_recogidas'];

         $this->rem_nombre = $this->userinfo['rem_nombre'];
         $this->remDepartamento = $this->userinfo['remDepartamento'];
         $this->remDireccion = $this->userinfo['remDireccion'];
         $this->remTelefono = $this->userinfo['remTelefono'];
         $this->remEmail = $this->userinfo['remEmail'];
         $this->remCp = $this->userinfo['remCp'];
         $this->remPob = $this->userinfo['remPob'];

         if(!$this->userfoto)  $this->userfoto = 'avatar.png';



         $_SESSION['userlevel']   = $this->userlevel;
         $_SESSION['empresa']    = $this->empresa;
         $_SESSION['departamento']       = $this->departamento;
         $_SESSION['pais']       = $this->pais;
         $_SESSION['userfoto']    = $this->userfoto;

         $_SESSION['empresa_GLS_UID']    = $this->empresa_GLS_UID;
         $_SESSION['codcli']    = $this->codcli;
         $_SESSION['empresa_logo']    = $this->empresa_logo;
         $_SESSION['empresa_logo_top']    = $this->empresa_logo_top;
         $_SESSION['empresa_logo_label']    = $this->empresa_logo_label;         
         $_SESSION['empresa_nombre']    = $this->empresa_nombre;
         $_SESSION['empresa_email_agencia']    = $this->empresa_email_agencia; 
         $_SESSION['UID_recogidas']    = $this->UID_recogidas; 
         $_SESSION['cp_recogidas']    = $this->cp_recogidas; 
         
         

         $_SESSION['rem_nombre']    = $this->rem_nombre;
         $_SESSION['remDepartamento']    = $this->remDepartamento;
         $_SESSION['remDireccion']    = $this->remDireccion;
         $_SESSION['remTelefono']    = $this->remTelefono;
         $_SESSION['remEmail']    = $this->remEmail;
         $_SESSION['remCp']    = $this->remCp;
         $_SESSION['remPob']    = $this->remPob;

         return true;
      }
      /* User not logged in */ else {
         return false;
      }
   }


   /**
    * login - The user has submitted his username and password
    * through the login form, this function checks the authenticity
    * of that information in the database and creates the session.
    * Effectively logging in the user if all goes well.
    */
   function login($subuser, $subpass, $subremember)
   {
      global $database, $form;  //The database and form object


      /* Checks username está activo */
      $subuser = stripslashes($subuser);
      $result = $database->UserInactive($subuser);
      if ($result == 1) {
         $field = "user";
         $form->setError($field, "Este usuario está marcado como inactivo");
         return false;
      }


      /* Username error checking */
      $field = "user";  //Use field name for username
      if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
         $form->setError($field, "Entra el usuario");
      } else {
         /* Check if username is not alphanumeric */
         //if(!eregi("^([0-9a-z])*$", $subuser)){
         //   $form->setError($field, "El nombre de usuario ha de ser alfanumérico");
         //}
      }

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if (!$subpass) {
         $form->setError($field, "Contraseña obligatoria");
      }

      /* Return if form errors exist */
      if ($form->num_errors > 0) {
         return false;
      }

      /* Checks that username is in database and password is correct */
      $subuser = stripslashes($subuser);
      $result = $database->confirmUserPass($subuser, md5($subpass));

      /* Check error codes */
      if ($result == 1) {
         $field = "user";
         $form->setError($field, "Usuario incorrecto");
      } else if ($result == 2) {
         $field = "pass";
         $form->setError($field, "Password incorrecto");
         // Grabar log
         $database->log_msg(LOG_USR, "Intento de LogIn del usuario $subuser con password erróneo  desde " . $_SERVER['REMOTE_ADDR']);
      } else if ($result == 3) {
         $field = "user";
         $form->setError($field, "Usuario inactivo");
         // Grabar log
         $database->log_msg(LOG_USR, "Intento de LogIn del usuario $subuser Marcado como NO activo desde " . $_SERVER['REMOTE_ADDR']);
      }

      /* Return if form errors exist */
      if ($form->num_errors > 0) {
         return false;
      }

      /* Username and password correct, register session variables */
      $this->userinfo  = $database->getUserInfo($subuser);
      $this->username  = $_SESSION['username'] = $this->userinfo['username'];
      $this->userid    = $_SESSION['userid']   = $this->generateRandID();
      $this->userlevel = $this->userinfo['userlevel'];
      $this->empresa   = $this->userinfo['empresa'];
      $this->departamento   = $this->userinfo['departamento'];
      $this->pais        = $this->userinfo['pais'];
      $this->userfoto  = $this->userinfo['userfoto'];

      //echo "user level: "+$this->userinfo['userlevel'];

      $_SESSION['userlevel'] = $this->userlevel;
      $_SESSION['empresa'] = $this->empresa;
      $_SESSION['departamento'] = $this->departamento;
      $_SESSION['pais'] = $this->pais;
      $_SESSION['userfoto'] = $this->userfoto;
      $_SESSION['last_activity'] = time(); // Me guardo el time de la última actividad

      /* Insert userid into database and update active users table */
      $database->updateUserField($this->username, "userid", $this->userid);
      $database->addActiveUser($this->username, $this->time);
      $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

      /**
       * This is the cool part: the user has requested that we remember that
       * he's logged in, so we set two cookies. One to hold his username,
       * and one to hold his random value userid. It expires by the time
       * specified in constants.php. Now, next time he comes to our site, we will
       * log him in automatically, but only if he didn't log out before he left.
       */
      if ($subremember) {
         setcookie("cookname", my_simple_crypt($this->username, 'e'), time() + COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   $this->userid,   time() + COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Login completed successfully */

      // Grabar log
      $database->log_msg(LOG_USR_LOGIN, "LogIn de usuario desde " . $_SERVER['REMOTE_ADDR']);

      return true;
   }

   /**
    * logout - Gets called when the user wants to be logged out of the
    * website. It deletes any cookies that were stored on the users
    * computer as a result of him wanting to be remembered, and also
    * unsets session variables and demotes his user level to guest.
    */
   function logout($auto)
   {
      global $database;  //The database connection
      /**
       * Delete cookies - the time must be in the past,
       * so just negate what you added when creating the
       * cookie.
       */
      if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
         setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
         setcookie("cookid",   "", time() - COOKIE_EXPIRE, COOKIE_PATH);
      }

      /* Grabar en la tabla de log el logged out */
      if ($auto) {
         $database->log_msg(LOG_USR_LOGIN, "Tiempo de inactividad de " . USER_TIMEOUT .
            " minutos superado por el usuario, desconexión automática del sistema");
         $_SESSION['logout'] = "Auto";
      } else {
         $database->log_msg(LOG_USR_LOGIN, "LogOut del usuario");
         $_SESSION['logout'] = "Manual";
      }

      /* Unset PHP session variables */
      unset($_SESSION['username']);
      unset($_SESSION['userid']);

      unset($_SESSION['userlevel']);
      unset($_SESSION['empresa']);
      unset($_SESSION['departamento']);
      unset($_SESSION['pais']);
      unset($_SESSION['userfoto']);
      unset($_SESSION['EditUsuarios']);

      /* Reflect fact that user has logged out */
      $this->logged_in = false;

      /**
       * Remove from active users table and add to
       * active guests tables.
       */
      $database->removeActiveUser($this->username);
      $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

      /* Set user level to guest */
      $this->username  = GUEST_NAME;
      $this->userlevel = GUEST_LEVEL;
   }

   /**
    * setPass - Set password user
    * 1. If no errors were found, it changes the password user and
    * returns 0. Returns 2 if change failed.
    */
   function setPass($subpass, $subpass2, $code, $username)
   {
      global $database, $form, $mailer;  //The database, form and mailer object

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if (!$subpass) {
         $form->setError($field, "Password is required");
      } else {
         /* Spruce up password and check length*/
         $subpass = stripslashes($subpass);
         if (strlen($subpass) < 4) {
            $form->setError($field, "Password too short");
         }
         /* Check if password is not alphanumeric */ else if (!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))) {
            $form->setError($field, "Password is not alphanumeric");
         }
         /**
          * Note: I trimmed the password only after I checked the length
          * because if you fill the password field up with spaces
          * it looks like a lot more characters than 4, so it looks
          * kind of stupid to report "password too short".
          */
      }

      /* Password2 error checking */
      $field = "pass2";  //Use field name for password
      if (!$subpass2) {
         $form->setError($field, "Password Required");
      } else if ($subpass <> $subpass2) {
         $form->setError($field, "Passwords do not match");
      }

      /* Errors exist, have user correct them */
      if ($form->num_errors > 0) {
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */ else {

         if ($database->changePassword($username, md5($subpass))) {
            return 0;  //Password modified succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }

   /**
    * setFirstPass - Set password user
    * 1. If no errors were found, it changes the password user and
    * returns 0. Returns 2 if change failed.
    */
   function setFirstPass($subpass, $subpass2, $userid, $username)
   {
      global $database, $form, $mailer;  //The database, form and mailer object

      /* Password error checking */
      $field = "pass";  //Use field name for password
      if (!$subpass) {
         $form->setError($field, "Contraseña obligatoria");
      } else {
         /* Spruce up password and check length*/
         $subpass = stripslashes($subpass);
         if (strlen($subpass) < 4) {
            $form->setError($field, "Password demasiado corto");
         }
         /* Check if password is not alphanumeric */ else if (!eregi("^([0-9a-z])+$", ($subpass = trim($subpass)))) {
            $form->setError($field, "Password no es alfanumérico");
         }
         /**
          * Note: I trimmed the password only after I checked the length
          * because if you fill the password field up with spaces
          * it looks like a lot more characters than 4, so it looks
          * kind of stupid to report "password too short".
          */
      }

      /* Password2 error checking */
      $field = "pass2";  //Use field name for password
      if (!$subpass2) {
         $form->setError($field, "Password required");
      } else if ($subpass <> $subpass2) {
         $form->setError($field, "Password's not match");
      }

      /* Errors exist, have user correct them */
      if ($form->num_errors > 0) {
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */ else {

         if ($database->changePassword($username, md5($subpass))) {
            return 0;  //Password modified succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }



   /**
    * register - Gets called when the user has just submitted the
    * registration form. Determines if there were any errors with
    * the entry fields, if so, it records the errors and returns
    * 1. If no errors were found, it registers the new user and
    * returns 0. Returns 2 if registration failed.
    */
   function register($username, $usuari_nom, $telefon, $empresa, $departamento, $pais, $userlevel)
   {
      global $database, $form, $mailer;  //The database, form and mailer object

      // echo "estoy en register de sesion, y subcarreg=$subcarreg";exit();

      /* Username error checking */
      $field = "username";  //Use field name for username
      if (!$username || strlen($username = trim($username)) == 0) {
         $form->setError($field, "User required");
      }


      /* Errors exist, have user correct them */
      if ($form->num_errors > 0) {
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */ else {

         //echo "estoy en register de sesion NO HAY Errores <br> subuser=$subuser <br> subcarreg=$subcarreg";exit();

         if ($database->addNewUser($username, $usuari_nom, $telefon, $empresa, $code, $pais, $userlevel)) {

            $userinfo = $database->getUserInfo($username);
            //echo "<pre>";
            //var_dump($userinfo);
            //echo "</pre>";
            //exit();
            if (EMAIL_WELCOME) {
               $mailer->sendWelcome($username, $username, $userinfo['userid']);
            }
            return 0;  //New user added succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }

   /**
    * createWebmobile - Gets called when the user has just submitted the
    * create form. Determines if there were any errors with
    * the entry fields, if so, it records the errors and returns
    * 1. If no errors were found, it registers the new user and
    * returns 0. Returns 2 if registration failed.
    */
   function createWebmobile($subwebmobile, $subprojectid, $subclientid, $subidCat, $subidEsp, $subidEng, $subxlsActivitat, $subxlsInactivitat)
   {
      global $database, $form, $mailer;  //The database, form and mailer object
      $allowedExtensions = array("xls", "xlsx");

      /* Webmobile error checking */
      $field = "webmobile";  //Use field name for webmobile
      if (!$subwebmobile || strlen($subwebmobile = trim($subwebmobile)) == 0) {
         $form->setError($field, "Campo obligatorio");
      } else {
         /* Check if webmobile is already in use */
         if ($database->webmobileTaken($subwebmobile)) {
            $form->setError($field, "* Descripcio en &uacute;s a altre webmobile");
         }
      }
      /* Language error checking */
      $field = "idCat";  //Use field name for idCat
      if (!$subidCat && !$subidEsp && !$subidEng) {
         $form->setError($field, "* Com a m&iacute;nim has de seleccionar un idioma");
      }

      /* xlsActivitat error checking */
      $field = "xlsActivitat";  //Use field name for xlsActivitat
      if ($subxlsActivitat) {
         if (!in_array(end(explode(".", strtolower($subxlsActivitat))), $allowedExtensions)) {
            $form->setError($field, "* El fitxer ha d'estar en format Full de c&agrave;lcul (xls o xlsx)");
         }
      }

      /* xlsInactivitat error checking */
      $field = "xlsInactivitat";  //Use field name for xlsActivitat
      if ($subxlsInactivitat) {
         if (!in_array(end(explode(".", strtolower($subxlsInactivitat))), $allowedExtensions)) {
            $form->setError($field, "* El fitxer ha d'estar en format Full de c&agrave;lcul (xls o xlsx)");
         }
      }

      /* Errors exist, have user correct them */
      if ($form->num_errors > 0) {
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */ else {
         //echo $subuser;
         //$subclientid = $databasenfc->getIdClientByIdContrato($subprojectid);
         if ($database->addNewWebmobile($subwebmobile, $subclientid, $subprojectid, $subidCat, $subidEsp, $subidEng)) {
            //if(EMAIL_WELCOME){
            //   $mailer->sendWelcome($subuser,$subemail,$subpass);
            //}
            return 0;  //New user added succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }

   /**
    * createWebmobile - Gets called when the user has just submitted the
    * create form. Determines if there were any errors with
    * the entry fields, if so, it records the errors and returns
    * 1. If no errors were found, it registers the new user and
    * returns 0. Returns 2 if registration failed.
    */
   function editWebmobile($subwebmobileid, $subwebmobile, $subclientid, $subprojectid, $subidCat, $subidEsp, $subidEng, $subxlsActivitat, $subxlsInactivitat)
   {
      global $database, $form, $mailer;  //The database, form and mailer object
      $allowedExtensions = array("xls", "xlsx");

      /* Webmobile error checking */
      $field = "webmobile";  //Use field name for webmobile
      if (!$subwebmobile || strlen($subwebmobile = trim($subwebmobile)) == 0) {
         $form->setError($field, "* Camp obligatori");
      }

      /* Language error checking */
      $field = "idCat";  //Use field name for idCat
      if (!$subidCat && !$subidEsp && !$subidEng) {
         $form->setError($field, "* Com a m&iacute;nim has de seleccionar un idioma");
      }

      /* xlsActivitat error checking */
      $field = "xlsActivitat";  //Use field name for xlsActivitat
      if ($subxlsActivitat) {
         if (!in_array(end(explode(".", strtolower($subxlsActivitat))), $allowedExtensions)) {
            $form->setError($field, "* El fitxer ha d'estar en format Full de c&agrave;lcul (xls o xlsx)");
         }
      }

      /* xlsInactivitat error checking */
      $field = "xlsInactivitat";  //Use field name for xlsActivitat
      if ($subxlsInactivitat) {
         if (!in_array(end(explode(".", strtolower($subxlsInactivitat))), $allowedExtensions)) {
            $form->setError($field, "* El fitxer ha d'estar en format Full de c&agrave;lcul (xls o xlsx)");
         }
      }

      /* Errors exist, have user correct them */
      if ($form->num_errors > 0) {
         return 1;  //Errors with form
      }
      /* No errors, add the new account to the */ else {
         //echo $subuser;
         if ($database->editWebmobile($subwebmobileid, $subwebmobile, $subclientid, $subprojectid, $subidCat, $subidEsp, $subidEng)) {
            //if(EMAIL_WELCOME){
            //   $mailer->sendWelcome($subuser,$subemail,$subpass);
            //}
            return 0;  //New user added succesfully
         } else {
            return 2;  //Registration attempt failed
         }
      }
   }


   function uploadFileRegister($webmobile, $success, $filepath, $field, $dbfield)
   {
      global $database, $form;

      if ($success) {
         $return = $database->updateFilename($webmobile, $filepath, $dbfield);
      } else {
         $form->setError($field, "* No s'ha pogut pujar el fitxer tot i que la resta de dades s'han enregistrat correctament. <br/>Editi aquest webmobile per intentar tornar a pujar el fitxer.");
      }
   }

   /**
    * editAccount - Attempts to edit the user's account information
    * including the password, which it first makes sure is correct
    * if entered, if so and the new password is in the right
    * format, the change is made. All other fields are changed
    * automatically.
    */
   function editAccount($username, $usuari_nom, $telefon, $empresa, $departamento, $pais, $userlevel, $activo)
   {
      global $database, $form;  //The database and form object


      /* Check if mail is already in use */
      if ($database->mailTakenException($username, $username)) {
         $form->setError($field, "* Este eMail ya está en uso");
      }



      //echo $form->num_errors;

      /* Errors exist, have user correct them */
      if ($form->num_errors > 0) {
         return false;  //Errors with form
      }



      /* Grabar resto de cambios */
      if ($usuari_nom) {
         $database->updateUserField($username, "usuari_nom", $usuari_nom);
      }
      if ($userlevel) {
         $database->updateUserField($username, "userlevel", $userlevel);
      }
      if ($telefon) {
         $database->updateUserField($username, "telefon", $telefon);
      }
      if ($empresa) {
         $database->updateUserField($username, "empresa", $empresa);
      }
      if ($departamento) {
         $database->updateUserField($username, "departamento", $departamento);
      }
      if ($pais) {
         $database->updateUserField($username, "pais", $pais);
      }
      if ($userfoto) {
         $database->updateUserField($username, "userfoto", $userfoto);
      }
      if ($activo == "") $activo = 0;
      $database->updateUserField($username, "activo", $activo);


      /*
	echo "<pre>";
	var_dump($form);
	echo "</pre>";
	   echo "empresa=$empresa<br>";
	   echo "activo=$activo<br>";
	   exit();
	   */

      /* Success! */
      return true;
   }

   /**
    * isAdmin - Returns true if currently logged in user is
    * an administrator, false otherwise.
    */
   function isAdmin()
   {
      return ($this->userlevel == ADMIN_LEVEL ||
         $this->username  == ADMIN_NAME);
   }

   /**
    * generateRandID - Generates a string made up of randomized
    * letters (lower and upper case) and digits and returns
    * the md5 hash of it to be used as a userid.
    */
   function generateRandID()
   {
      return md5($this->generateRandStr(16));
   }

   /**
    * generateRandStr - Generates a string made up of randomized
    * letters (lower and upper case) and digits, the length
    * is a specified parameter.
    */
   function generateRandStr($length)
   {
      $randstr = "";
      for ($i = 0; $i < $length; $i++) {
         $randnum = mt_rand(0, 61);
         if ($randnum < 10) {
            $randstr .= chr($randnum + 48);
         } else if ($randnum < 36) {
            $randstr .= chr($randnum + 55);
         } else {
            $randstr .= chr($randnum + 61);
         }
      }
      return $randstr;
   }

   /**
    * removeAccents - Removes accents of a string
    */
   function removeAccents($str)
   {
      $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
      $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
      return str_replace($a, $b, $str);
   }
};


/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;



/**
 * Configuración de empresa 
 */

// No especifican logo en la tabla
if (!$_SESSION['empresa_logo']) $_SESSION['empresa_logo'] = "gls_logo.png";
if (!$_SESSION['empresa_logo_top']) $_SESSION['empresa_logo_top'] = "gls_logoTop.png";
if (!$_SESSION['empresa_logo_label']) $_SESSION['empresa_logo_label'] = "";

// Especifican logo pero NO existe el archivo
if (!file_exists("data/logos/" . $_SESSION['empresa_logo'])) $_SESSION['empresa_logo'] = "gls_logo.png";
if (!file_exists("data/logos/" . $_SESSION['empresa_logo_top'])) $_SESSION['empresa_logo_top'] = "gls_logoTop.png";
if (!file_exists("data/logos/" . $_SESSION['empresa_logo_label'])) $_SESSION['empresa_logo_label'] = "";

$APP_TITLE = ucwords($_SESSION['empresa_nombre']) . " Gestión de envíos";
$HEAD_TITLE = ucwords($_SESSION['empresa_nombre']) . " Gestión de <b>envíos</b>";
$LOGO = $_SESSION['empresa_logo'];
$LOGO_TOP = $_SESSION['empresa_logo_top'];
$LOGO_LABEL = $_SESSION['empresa_logo_label'];