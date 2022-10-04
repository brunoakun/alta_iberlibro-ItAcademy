<? // Funciones generales en PHP


/***************************************************
    Retorna un texto existente entre 2 strings
****************************************************/
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}


/***************************************************
	Desinfecta Strings de Inputs antes de procesarlos
****************************************************/
function desinfecta($string) {
    $string = str_replace("'", "´", $string); 
    $string = str_replace('"', "´", $string); 
	return(filter_var ($string, FILTER_SANITIZE_STRING)); 
}


/***************************************************
	Comprueba validez de CIF, NIF,NIE
****************************************************/
function CHKnif($dni) {
 if(strlen($dni)<9) {
		return false;
	}
 
	$dni = strtoupper($dni);
 
	$letra = substr($dni, -1, 1);
	$numero = substr($dni, 0, 8);
 
	// Si es un NIE hay que cambiar la primera letra por 0, 1 � 2 dependiendo de si es X, Y o Z.
	$numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);	
 
	$modulo = $numero % 23;
	$letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
	$letra_correcta = substr($letras_validas, $modulo, 1);
 
	if($letra_correcta!=$letra) {
		return false;
	}
	else {
		return true;
	}
}


/***************************************************
	Deveulve la IP del cliente
****************************************************/
function get_IP_address()
{
   $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

/***************************************************
	Comprobar si una cuenta IBAN es correcta
****************************************************/

function comprobar_iban($iban)
{
    # definimos un array de valores con el valor de cada letra
    $letras=array("A"=>10, "B"=>11, "C"=>12, "D"=>13, "E"=>14, "F"=>15, "G"=>16,"H"=>17, "I"=>18, "J"=>19, "K"=>20, "L"=>21, "M"=>22, "N"=>23, "O"=>24, "P"=>25, "Q"=>26, "R"=>27, "S"=>28, "T"=>29, "U"=>30, "V"=>31, "W"=>32, "X"=>33, "Y"=>34, "Z"=>35);
 
    # Eliminamos los posibles espacios al inicio y final
    $iban=trim($iban);
 
    # Convertimos en mayusculas
    $iban=strtoupper($iban);
 
    # eliminamos espacio y guiones que haya en el iban
    $iban=str_replace(array(" ","-"),"",$iban);
 
    if(strlen($iban)==24)
    {
        # obtenemos los codigos de las dos letras
        $valorLetra1 = $letras[substr($iban, 0, 1)];
        $valorLetra2 = $letras[substr($iban, 1, 1)];
 
        # obtenemos los siguientes dos valores
        $siguienteNumeros= substr($iban, 2, 2);
 
        $valor = substr($iban, 4, strlen($iban)).$valorLetra1.$valorLetra2.$siguienteNumeros;
 
        if(bcmod($valor,97)==1)
        {
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}


/***************************************************
	Devuelve el día de la semana en letras
****************************************************/
function txtSemana($aux){
	$dias = array("domingo","lunes","martes","mi&eacute;rcoles","jueves","viernes","s&aacute;bado");
	$texto=$dias[date("w",strtotime($aux))];
	return($texto);
}

/***************************************************
	Devuelve el mes en letras
****************************************************/
function txtMes($aux){
	$meses = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
	$texto=$meses[date("n",strtotime($aux))-1];
	return($texto);
}


/***************************************************
	Formatear mensaje de error
****************************************************/
function cssError($texto){
		$aux = "";
		$aux = '<div class="formError" style="opacity: 0.87; position: absolute; top: -5px; left: 100px; margin-top: -7px;"><div class="formErrorContent">'.$texto.'<br></div><div class="formErrorArrow"><div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div></div></div>';
	return($aux);
}


/***************************************************
	Validar si un eMail es correcto
****************************************************/
function esEmail($direccion)
{
   $Sintaxis='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
   if(preg_match($Sintaxis,$direccion))
      return true;
   else
     return false;
}


/***************************************************
	Devuelve un array con info de un fichero del disco
****************************************************/
function filedata($path) {
/*
Ejemplo para un Nombre simpl�n: name.jpeg
Array (7)
(
    ['exists'] = Boolean(1) TRUE
    ['writable'] = Boolean(0) FALSE
    ['chmod'] = String(4) � 0644 �
    ['ext'] = String(4) � jpeg �
    ['path'] = Boolean(0) FALSE
    ['name'] = String(4) � name �
    ['filename'] = String(9) � name.jpeg �
)
*/
        clearstatcache(); 
        $data["exists"] = is_file($path); 
        $data["writable"] = is_writable($path); 
        $data["chmod"] = ($data["exists"] ? substr(sprintf("%o", fileperms($path)), -4) : FALSE); 
        $data["ext"] = substr(strrchr($path, "."),1); 
        $data["path"] = array_shift(explode(".".$data["ext"],$path)); 
        $data["name"] = array_pop(explode("/",$data["path"])); 
        $data["name"] = ($data["name"] ? $data["name"] : FALSE);
        $data["path"] = ($data["exists"] ? ($data["name"] ? realpath(array_shift(explode($data["name"],$data["path"]))) : realpath(array_shift(explode($data["ext"],$data["path"])))) : ($data["name"] ? array_shift(explode($data["name"],$data["path"])) : ($data["ext"] ? array_shift(explode($data["ext"],$data["path"])) : rtrim($data["path"],"/")))) ; 
        $data["filename"] = (($data["name"] OR $data["ext"]) ? $data["name"].($data["ext"] ? "." : "").$data["ext"] : FALSE); 
        return $data; 
}


/***************************************************
	Valida tipo de file para foto Upload
****************************************************/
function EsFoto($file){
	if (($file == "image/gif")
	||  ($file == "image/jpeg")
	||  ($file == "image/jpg")
	||  ($file == "image/pjpeg")
	||  ($file == "image/x-png")
	||  ($file == "image/png")){
		return(true);
	}else{
		return(false);
	}
}
?>