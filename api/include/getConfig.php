<?
// Cargar valores de configuración en sesion

$rec = $database->getQuery("SELECT * FROM ".TBL_CONFIG.";");

$_SESSION['config_email_suport'] 		=  $rec[0]['email_suport']; 
$_SESSION['config_max_kb_fotos'] 		=  $rec[0]['max_kb_fotos']; 
?>