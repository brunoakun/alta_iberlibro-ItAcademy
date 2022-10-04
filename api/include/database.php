<?

/**
 * Database.php
 *
 * Verión PHP 7.x
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 */
include("x-init.php");
include("constants.php");

class MySQLDB
{
   var $connection;         //The MySQL database connection
   var $num_active_users;   //Number of active users viewing site
   var $num_active_guests;  //Number of active guests viewing site
   var $num_members;        //Number of signed-up users
   /* Note: call getNumMembers() to access $num_members! */

   /* Class constructor */
   //function MySQLDB(){
   function __construct()
   {
      /* Make connection to database */
      $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysqli_connect_error());
      mysqli_set_charset($this->connection, 'utf8');
      mysqli_select_db($this->connection, DB_NAME) or die(mysqli_connect_error());


      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;

      if (TRACK_VISITORS) {
         /* Calculate number of users at site */
         $this->calcNumActiveUsers();

         /* Calculate number of guests at site */
         $this->calcNumActiveGuests();
      }
   }



   /**
    * escape_str - Escapa carateres especiales 
    * Antes de Inserts y Updates
    */
   function escape_str($str)
   {
      $result = mysqli_real_escape_string($this->connection, $str);
      return $result;
   }

   
   
   /**
    * last_id - Devuelve el último Id del registro insertado
    */    
    function last_id(){
      return mysqli_insert_id($this->connection);
   }
 
 




   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password)
   {
      /* Add slashes if necessary (for query) */
      if (!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT password, activo FROM " . TBL_USERS . " WHERE username = '$username'";
      $result = mysqli_query($this->connection, $q);
      $dbarray = mysqli_fetch_array($result);

      if (!$result || (mysqli_num_rows($result) < 1)) {
         return 1; //Indicates username failure
      }

      if ($dbarray['activo'] == 0) {
         return 3; //Indicates username no sctivo
      }

      /* Retrieve password from result, strip slashes */
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      /* Validate that password is correct */
      if ($password == $dbarray['password']) {
         return 0; //Success! Username and password confirmed
      } else {
         return 2; //Indicates password failure
      }
   }

   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserIDOld($username, $userid)
   {
      /* Add slashes if necessary (for query) */
      if (!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT userid FROM " . TBL_USERS . " WHERE username = '$username'";
      $result = mysqli_query($this->connection, $q);
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return 1; //Indicates username failure
      }

      /* Retrieve userid from result, strip slashes */
      $dbarray = mysqli_fetch_array($result);
      $dbarray['userid'] = stripslashes($dbarray['userid']);
      $userid = stripslashes($userid);

      /* Validate that userid is correct */
      if ($userid == $dbarray['userid']) {
         return 0; //Success! Username and userid confirmed
      } else {
         return 2; //Indicates userid invalid
      }
   }

   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserID($username, $userid)
   {
      /* Add slashes if necessary (for query) */
      if (!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT userid FROM " . TBL_USERS . " WHERE username = '$username'";
      $result = mysqli_query($this->connection, $q);
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return 1; //Indicates username failure
      } else {
         return 0; //Success! Username and userid confirmed
      }
   }



   /**
    * confirmUserActive - Checks que el usuario 
    * est�  marcado como ACTIVO="SI" en la BBDD
    * Si el usr est� SI returns 1.
    * Si el usr est� NO returns 0.
    */
   function UserInactive($username)
   {
      /* Add slashes if necessary (for query) */
      if (!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }

      /* Verify that user is in database */
      $q = "SELECT * FROM " . TBL_USERS . " WHERE username = '$username' AND activo = 0";
      $result = mysqli_query($this->connection, $q);
      //echo "q=$q<br>"; echo mysqli_num_rows($result); exit();
      return (mysqli_num_rows($result) > 0);
   }


   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username)
   {
      if (!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }
      $q = "SELECT username FROM " . TBL_USERS . " WHERE username = '$username'";
      $result = mysqli_query($this->connection, $q);
      return (mysqli_num_rows($result) > 0);
   }

   /**
    * mailTaken - Returns true if the mail has
    * been taken by another user, false otherwise.
    */
   function mailTaken($email)
   {
      if (!get_magic_quotes_gpc()) {
         $mail = addslashes($email);
      }
      $q = "SELECT email FROM " . TBL_USERS . " WHERE email = '$email'";
      $result = mysqli_query($this->connection, $q);
      return (mysqli_num_rows($result) > 0);
   }

   /**
    * mailTaken - Returns true if the mail has
    * been taken by another user, false otherwise.
    */
   function mailTakenException($email, $usernameException)
   {
      if (!get_magic_quotes_gpc()) {
         $mail = addslashes($email);
      }
      $q = "SELECT username FROM " . TBL_USERS . " WHERE username = '$email' and username<>'$usernameException'";
      $result = mysqli_query($this->connection, $q);
      return (mysqli_num_rows($result) > 0);
   }

   /**
    * checkCode - Returns true if the mail has
    * been taken by another user, false otherwise.
    */
   function checkCode($code)
   {
      $q = "SELECT code FROM " . TBL_USERS . " WHERE code = '$code'";
      $result = mysqli_query($this->connection, $q);
      return (mysqli_num_rows($result) > 0);
   }

   /**
    * getUserFromCode - Returns username from code
    */
   function getUserFromCode($code)
   {
      $q = "SELECT username FROM " . TBL_USERS . " WHERE code = '$code'";
      $result = mysqli_query($this->connection, $q);

      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username)
   {
      if (!get_magic_quotes_gpc()) {
         $username = addslashes($username);
      }
      $q = "SELECT username FROM " . TBL_BANNED_USERS . " WHERE username = '$username'";
      $result = mysqli_query($this->connection, $q);
      return (mysqli_num_rows($result) > 0);
   }

   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $usuari_nom, $telefon, $empresa, $userlevel)
   {
      $time = time();


      /* Registra en la tabla de log el alta */
      if (LOG_USR) {
         $Qlog = "INSERT into " . TBL_LOG . " SET time = " . time() . ", demarcacion = '" . $_SESSION['empresa_nombre'] .
            "', usuario = '" .  $_SESSION['username'] . "', texto = 'Alta del Usuari " . $username . "'";
         mysqli_query($Qlog, $this->connection);
         //echo "query= ".$Qlog; exit();
      }

      //$q = "INSERT INTO ".TBL_USERS." ('username', 'password', 'userid', 'userlevel', 'timestamp', 'usuari_nom', 'telefon', 'empresa', 'activo') VALUES ('$username', '', '0', $userlevel, $time,'$usuari_nom','$telefon','$empresa',0)";

      $q = "INSERT INTO `" . TBL_USERS . "` (`username`, `password`, `userid`, `userlevel`, `email`, `code`, `timestamp`, `demarcacion`, `activitat`, `per_defecte`, `usuari_nom`, `telefon`, `adreca`, `carreg`, `empresa`, `activo`) VALUES ('$username','',$time,$userlevel,'','',$time,'','','','$usuari_nom','$telefon','','','$empresa',0)";

      //echo $q;
      //exit();

      return mysqli_query($this->connection, $q);
   }

   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value)
   {
      $q = "UPDATE " . TBL_USERS . " SET " . $field . " = '$value' WHERE username = '$username'";
      return mysqli_query($this->connection, $q);
   }

   /**
    * changePassword - Change user's password
    */
   function changePassword($username, $pass)
   {
      $q = "UPDATE " . TBL_USERS . " SET password = '$pass', code='' WHERE username = '$username'";
      return mysqli_query($this->connection, $q);
   }



   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username)
   {
      //      $q = "SELECT * FROM ".TBL_USERS." WHERE username = '$username'";
      $q = "SELECT * FROM usuaris LEFT OUTER JOIN empresas ON usuaris.empresa = empresas.empresa_nombre WHERE usuaris.username = '$username'";
      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfoById($id)
   {
      $q = "SELECT * FROM " . TBL_USERS . " WHERE userid = '$id'";
      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   /**
    * getUserInfoFromMail - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given mail. If query fails, NULL is returned.
    */
   function getUserInfoFromMail($email)
   {
      $q = "SELECT * FROM " . TBL_USERS . " WHERE email = '$email'";
      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
   function getNumMembers()
   {
      if ($this->num_members < 0) {
         $q = "SELECT * FROM " . TBL_USERS;
         $result = mysqli_query($this->connection, $q);
         $this->num_members = mysqli_num_rows($result);
      }
      return $this->num_members;
   }

   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveUsers()
   {
      /* Calculate number of users at site */
      $q = "SELECT * FROM " . TBL_ACTIVE_USERS;
      $result = mysqli_query($this->connection, $q);
      $this->num_active_users = mysqli_num_rows($result);
   }

   /**
    * calcNumActiveGuests - Finds out how many active guests
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveGuests()
   {
      /* Calculate number of guests at site */
      $q = "SELECT * FROM " . TBL_ACTIVE_GUESTS;
      $result = mysqli_query($this->connection, $q);
      $this->num_active_guests = mysqli_num_rows($result);
   }

   /**
    * addActiveUser - Updates username's last active timestamp
    * in the database, and also adds him to the table of
    * active users, or updates timestamp if already there.
    */
   function addActiveUser($username, $time)
   {
      $q = "UPDATE " . TBL_USERS . " SET timestamp = '$time' WHERE username = '$username'";
      $aux = mysqli_query($this->connection, $q);

      if (!TRACK_VISITORS) return;
      $q = "select * FROM active_users where username = '$username'";
      $aux = $this->CuentaRegistros($q);

      if ($aux == 0) {
         $q = "REPLACE INTO " . TBL_ACTIVE_USERS . " VALUES ('$username', '$time')";
         mysqli_query($this->connection, $q);
      }
      $this->calcNumActiveUsers();
   }

   /* addActiveGuest - Adds guest to active guests table */
   function addActiveGuest($ip, $time)
   {
      if (!TRACK_VISITORS) return;
      $q = "REPLACE INTO " . TBL_ACTIVE_GUESTS . " VALUES ('$ip', '$time')";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveGuests();
   }

   /* These functions are self explanatory, no need for comments */

   /* removeActiveUser */
   function removeActiveUser($username)
   {
      if (!TRACK_VISITORS) return;
      $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE username = '$username'";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveUsers();
   }

   /* removeActiveGuest */
   function removeActiveGuest($ip)
   {
      if (!TRACK_VISITORS) return;
      $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE ip = '$ip'";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveGuests();
   }

   /* removeInactiveUsers */
   function removeInactiveUsers()
   {
      if (!TRACK_VISITORS) return;
      $timeout = time() - USER_TIMEOUT * 60;
      $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE timestamp < $timeout";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveUsers();
   }

   /* removeInactiveGuests */
   function removeInactiveGuests()
   {
      if (!TRACK_VISITORS) return;
      $timeout = time() - GUEST_TIMEOUT * 60;
      $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE timestamp < $timeout";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveGuests();
   }

   /**
    * getUsersList - Returns the result array from a mysql
    * query asking for all users. If query fails, NULL is returned.
    */
   function getUsersList()
   {


      $q = "SELECT * FROM " . TBL_USERS;

      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = array();
      //mysql_data_seek($result, 0);
      while ($row = mysqli_fetch_array($result)) {
         array_push($dbarray, $row);
      }
      //$dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   /**
    * getUsersAccess - Returns TRUE or FALSE
    * query asking if user have access to a webmobile
    */
   function getUsersAccess($webmobileid, $username)
   {

      $q = "SELECT * FROM " . TBL_USERS . "  WHERE webmobileid = '$webmobileid' AND username = '$username'";


      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return FALSE;
      } else {
         return TRUE;
      }
   }

   /**
    * getUsersList - Returns the result array from a mysql
    * query asking for all webmobiles. If query fails, NULL is returned.
    */
   function getWebmobilesList($webmobileid)
   {

      if ($webmobileid == 0) {
         $q = "SELECT * FROM " . TBL_WEBMOBILES . " ORDER BY webmobileid desc";
      } else {
         $q = "SELECT * FROM " . TBL_WEBMOBILES . " WHERE webmobileid = '$webmobileid' ORDER BY webmobileid  desc";
      }

      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = array();
      //mysql_data_seek($result, 0);
      while ($row = mysqli_fetch_array($result)) {
         array_push($dbarray, $row);
      }
      //$dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   /* deleteUser */
   function deleteUser($username)
   {
      $q = "DELETE FROM " . TBL_USERS . " WHERE username = '$username'";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveUsers();
   }

   /* deleteUserById */
   function deleteUserById($userid)
   {
      $q = "DELETE FROM " . TBL_USERS . " WHERE userid = '$userid'";
      mysqli_query($this->connection, $q);
      $this->calcNumActiveUsers();
   }

   /**
    * log_msg - Graba un registro en la tabla de log
    * Parametros: 
    * $grabar = Boleano definido en Contantes
    * $texto  = Texto a grabar
    */
   function log_msg($grabar, $texto)
   {
      if ($grabar) {
         $Qlog = "INSERT into " . TBL_LOG .
            "  SET time = " . time() .
            ", demarcacion = '" . $_SESSION['empresa_nombre'] . "'" .
            ", usuario =  '" . $_SESSION['username'] . "'" .
            ", texto = '" . $texto . "'";
         mysqli_query($this->connection, $Qlog);
         //echo "query= ".$Qlog; exit(); //bbwk
      }
      return true;
   }

   /**
    * getLogList - Returns the result array from a mysql
    * query asking for all log. If query fails, NULL is returned.
    */
   function getLogList($demarcacion)
   {

      if ($demarcacion == "0") {
         $q = "SELECT * FROM " . TBL_LOG;
      } else {
         $q = "SELECT * FROM " . TBL_LOG . " WHERE demarcacion = '$demarcacion'";
      }

      $result = mysqli_query($this->connection, $q);
      /* Error occurred, return given name by default */
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      /* Return result array */
      $dbarray = array();
      //mysql_data_seek($result, 0);
      while ($row = mysqli_fetch_array($result)) {
         array_push($dbarray, $row);
      }
      //$dbarray = mysqli_fetch_array($result);
      return $dbarray;
   }

   ////////////////////////////////////////////////////////////////////////////////////////////////
   ///////////////////////  TABLAS		////////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////////////////////////////////////////////

   /**
    * dbQuery - mysql QUERY SIN RETORNO DE VALORES
    * query asking for GENERICO
    */
   function dbQuery($q)
   {
      $result = mysqli_query($this->connection, $q);
      return NULL;
   }

   /**
    * getQuery - Returns the result array from a mysql QUERY
    * query asking for GENERICO
    */
   function getQuery($q)
   {
      $result = mysqli_query($this->connection, $q);
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      $dbarray = array();      //* Return result array
      while ($row = mysqli_fetch_array($result)) {
         array_push($dbarray, $row);
      }
      return $dbarray;
   }


   /**
    * getQueryAssoc - Returns the result array from a mysql QUERY
    * query asking for MYSQLI_ASSOC
    */
   function getQueryAssoc($q)
   {
      $result = mysqli_query($this->connection, $q);
      if (!$result || (mysqli_num_rows($result) < 1)) {
         return NULL;
      }
      $dbarray = array();      //* Return result array
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
         array_push($dbarray, $row);
      }
      return $dbarray;
   }

   /**
    * CuentaRegistros - COUNT de registros sobre un query
    * Parametros: 
    * $accion
    */
   function CuentaRegistros($q)
   {
      $result = mysqli_query($this->connection, $q);
      $conta = mysqli_num_rows($result);
      return $conta;
   }


   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function query($query)
   {
      return mysqli_query($this->connection, $query);
   }


   /**
    * cronQuery - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function cronQuery($query)
   {
      return mysqli_query($this->connection, $query);
   }

   /******************************************* WEB *****************************************************************/
};

/* Create database connection */
$database = new MySQLDB;
