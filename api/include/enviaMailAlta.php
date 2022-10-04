<?
function enviaMailAlta($email, $nombre, $nombreCompleto, $nif, $x_dir, $x_pob, $x_dir2, $x_pob2, $x_tel, $x_sexo, $x_fechaDeNacimiento, $x_titular, $TextoEmail){
	
	$x_dir2		= trim($x_dir2);
	
	$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
	$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$cabeceras .= "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
	
    $subject = "Confirmación de suscripción con Salvat";	
	
	// Texto del eMail	/////

$body = <<<'DELIMITER'

  <table style="border: solid; border-color:#d5d1d1" width="100%" cellspacing=0>
  <tbody style="color: #6e757b; font-size: 2; font-family: Gotham, Helvetica Neue, Helvetica, Arial,' sans-serif'">
         <tr>
      <td align="center"><p><img src="https://www.salvat.com/Content/Images/logo.gif" alt="Salvat.com"/>
		</td>
    </tr>
        <tr>
      <td>
          <table width="100%" border="4" cellspacing="0" bordercolor="#111783">
  <tbody>
    <tr >
      <td>	  
	  <br>	  
DELIMITER
?>
<?
//$body = $body."Hola <br><br>";
$body = $body.$TextoEmail;
$body = $body. <<<'DELIMITER'
<br><br>
		<b><u>Tus datos:</u></b><br><br>		  
Estos son los datos que hemos recibido:<br><br>
<table width="100%" border="0">
  <tbody>
    <tr>
      <td width="1%">Nombre: </td>
DELIMITER
?>
<?	
$body = $body. "<td>$nombreCompleto</td>
 </tr>";	
		
if($nif <> ""){	
	$body = $body. "   
    <tr>
      <td>NIF&nbsp;/&nbsp;CIF:</td>
      <td>$nif</td>
    </tr>";
}	
		
$body = $body. "    
	<tr>     
      <td>Dirección:</td>
      <td>$x_dir</td>
    </tr> 
   
   <tr>
      <td> </td>
      <td>$x_pob</td>
    </tr>";
	
	
if($x_dir2){
	$body = $body. "
	<tr>
	  <td>Dirección de envío adicional:</td>
	  <td valign='bottom'> $x_dir2</td>
	</tr> 

   <tr>
	  <td> </td>
	  <td>$x_pob2</td>
	</tr>";
}
	
	
	$body = $body. "  
	<tr>     
      <td>Teléfono:</td>
      <td>$x_tel</td>
    </tr> 
	
	<tr>     
      <td>Sexo:</td>
      <td>$x_sexo</td>
    </tr>";
	
	
if($x_fechaDeNacimiento<>"NULL"){
	$body = $body. " 	
	<tr>     
	  <td>Fecha&nbsp;de&nbsp;nacimiento:</td>
	  <td>$x_fechaDeNacimiento</td>
	</tr> ";
}
	
	
if($x_titular){
	$body = $body. "
	<tr>     
	  <td>Titular&nbsp;de&nbsp;la&nbsp;Cuenta&nbsp;Bancaria:</td>
	  <td>$x_titular</td>
	</tr> 	";
}
	
$body = $body. "
  </tbody>
</table>
<br>
Si algún dato no está correcto o lo quieres modificar por favor contacta con nosotros al 900.842.421 o a través del e-mail infosalvat@salvat.com<br><br>";

$any=date ("Y");
$body = $body. '<p align="center">'." SALVAT 2004-$any. Todos los derechos reservados.</p>
   </td>
    </tr>
  </tbody>
</table>
		</td>
    </tr>
  </tbody>
</table>";
?>
<?
      return mail($email ,$subject,$body,$cabeceras);
}	
?>	