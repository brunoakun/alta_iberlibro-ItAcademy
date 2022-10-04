<?
include("include/session.php");
include("include/func1.php");
include("include/_enviar_email.php");

$accion = $_GET["accion"];
$datos = array();
$estado = '';
$mensaje = '';

switch ($accion) {
	case "":
		/**
		 * Error, no se pasa acción
		 */
		$estado = "error";
		$mensaje = "No se ha pasado acción";
		break;

	case "add_solicitud":
		/**
		 * Capturar datos del form
		 * Grabar datos del formulario en la tabla
		 * Enviar email
		 * Devolver el contador "99999"
		 */

		# 1.- Capturar datos del form
		$form_data  = json_decode(file_get_contents("php://input"));

		$nombreFiscal	=  $database->escape_str($form_data->nombreFiscal);
		$nombreComercial =  $database->escape_str($form_data->nombreComercial);
		$dir			=  $database->escape_str($form_data->dir);
		$dirCp			= $form_data->dirCp;
		$dirPob			= $form_data->dirPob;
		$dirPro			= $form_data->dirPro;
		$contacto		= $database->escape_str($form_data->contacto);
		$eMail			= $form_data->eMail;
		$telefono 		= $form_data->telefono;
		$iban			= $form_data->iban;
		$nif			= $form_data->nif;
		$codCli			= $form_data->codCli;
		$recogida		= $form_data->recogida;
		$seguro			= $form_data->seguro;
		$tarifa			= $form_data->tarifa;

		# 2.- Formatear datos
		$nif 	= strtoupper($nif);
		$codCli = strtoupper($codCli);
		if ($recogida == '1') $recogida = 'Mañanas 10:00-13:00h';
		if ($recogida == '2') $recogida = 'Tardes 14:00-16:00h';
		if ($recogida == '0') $recogida = 'Indiferente 10:00-16:00h';

		if ($seguro == '1') $seguro = 'Sin seguro';
		if ($seguro == '2') $seguro = 'Seguro opcional 8%';
		if ($seguro == '0') $seguro = 'Seguro todo riesgo';

		if ($tarifa == '1') $tarifa = 'Menos de 6 envíos diarios';
		if ($tarifa == '2') $tarifa = 'Más de 6 envíos diarios';


		# 3.- Grabar datos en la tabla solicitudes_registro
		$q  = 'INSERT into solicitudes_registro ';
		$q .= '(nombreFiscal, nombreComercial, dir, dirCp,dirPob,dirPro,contacto,eMail,telefono,iban,nif,codCli,recogida,seguro,tarifa) ';
		$q .= 'VALUES ';
		$q .= "('$nombreFiscal', '$nombreComercial', '$dir', '$dirCp','$dirPob','$dirPro','$contacto','$eMail','$telefono','$iban','$nif','$codCli','$recogida','$seguro','$tarifa'); ";
		$auxInsert = $database->query($q);

		# 4.- Añadir el contacor
		$last_id = $database->last_id();
		if ($last_id) {
			$auxLast_id = str_pad($last_id, 5, "0", STR_PAD_LEFT);		// Llenar con '0' por la izquierda
			$q2 = "UPDATE solicitudes_registro SET contador='$auxLast_id' WHERE id=$last_id; ";
			$database->query($q2);
		}


		# 4.- enviar eMail 
		$body  .= "
		<table class='table table-striped'>    
		<tbody>
		<tr>
			<td valign='top'><b><h1>Departamento:</h1></b></td>
			<td><h1> $auxLast_id </h1></td>
		</tr> 

		<tr>
			<td valign='top'><b>Nombre fiscal:</b></td>
			<td> $nombreFiscal </td>
		</tr>  
		<tr>
			<td valign='top'><b>Nombre comercial:</b></td>
			<td> $nombreComercial <br></td>
		</tr>        
		<tr>
			<td valign='top'><b>Dirección:</b></td>
			<td> $dir  <br> $dirCp - $dirPob  ($dirPro)</td>
		</tr>
		<br>

		<tr>
			<td><b>Contacto:</b></td>
			<td> $contacto </td>
		</tr>      
		<tr>
			<td><b>Cód. cliente IberLibro:</b></td>
			<td> $codCli </td>
		</tr> 
		<tr>
			<td><b>Nif/Nie:</b></td>
			<td> $nif </td>
		</tr>  
		<tr>			 
			<td valign='top'><b>Teléfono y eMail:</b></td>
				<td> $telefono $eMail </td>
		</tr>	
		<br>

		<tr>			 
			<td valign='top'><b>Iban:</b></td>
			<td> $iban</td>
		</tr>
		<tr>			 
			<td><b>Envíos diarios:</b></td>
			<td> $tarifa</td>
		</tr>	
		<tr>			 
			<td><b>Cobertura de seguro:</b></td>
			<td> $seguro</td>
		</tr>		
		<tr>			 
			<td><b>Recogidas:</b></td>
			<td> $recogida</td>
		</tr>

	   </tbody>
	  </table>
	  ";
		$asunto = "Nuevo formulario de solicitud de alta " . time();
		$auxEnvio = $enviarEmail->sendEmail(EMAIL_SEND, $asunto, $body);

		$estado = "ok";

		if (!$auxInsert) {
			$estado = "error";
			$mensaje = "No se han podido insertar datos";
		}
		if ($auxEnvio <> 'OK') {
			$estado = "error";
			$mensaje = " Error enviando eMail: $auxEnvio";
		}

		// Montar array de respuesta 
		$datos = array(
			'last_id' => $last_id,
			'body' => $body,
			'q' => $q
		);

		if ($estado == "ok") $mensaje = "Solicitud enviada con número: $auxLast_id";
		break;

	case "busca_cp":
		/**
		 * Buscar Poblacion/es y Provincia en base a un CP
		 * Devolver array de Poblaciones y datos de provinvia
		 */

		$cp = $_GET["cp"];
		$estado = "ok";
		if (!$cp) {
			$estado = "error";
			$mensaje = "No se ha pasado cp";
		}
		$aux = substr($cp, 0, 2);
		$q1 = "SELECT * from aux_provincias WHERE cod_pro='$aux' ";
		$List = $database->getQuery($q1);
		$provincia = $List[0]['provincia'];

		$q = "SELECT * from poblacionesenvialia WHERE CP='$cp' ORDER BY Poblacion ";
		$List = $database->getQueryAssoc($q);

		if (!$List) $estado = "error";

		// Montar array de respuesta 
		$datos = array(
			'lista' => $List,
			'provincia' => $provincia,
			'q' => $q1
		);
		break;

	default:
		$estado = "error";
		$mensaje = "Acción no encontrada";
}




// Añadir estado al array de datos
$datos["estado"] = $estado;
$datos["mensaje"] = $mensaje;
$datos["accion"] = $accion;


//	CABECERAS	//

header('Content-Type: application/json');

// Evitar error blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource
header('Access-Control-Allow-Origin: *');

//Devolvemos el array pasado a JSON como objeto
http_response_code(200);


//echo json_encode($datos, JSON_FORCE_OBJECT);
echo json_encode($datos);
