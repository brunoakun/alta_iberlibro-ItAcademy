<?php

/**
 * Clase para la gestión de datos 
 * 23/12/2021 V1.0
 * 
 */


define("WS_UID", $_SESSION['empresa_GLS_UID']);
include_once("database.php");

class datos
{
    function __construct()
    {
        $this->time = time();
        $this->empresa = $_SESSION["empresa"];
        $this->empresa_GLS_UID = $_SESSION["empresa_GLS_UID"];
        $this->codcli = $_SESSION["codcli"];
    }


    /*******************************
     * getDataEmp (empresa_nombre)
     * Devuleve datos de la empresa
     */
    function getDataEmp($empresa_nombre)
    {
        global $database;
        $aux = $this->empresa;
        $q = "SELECT * FROM empresas WHERE empresa_nombre='$aux'";
        $ListEmpresa = $database->getQuery($q);
        $ListEmpresa[0]['remPro'] = $this->getProv($ListEmpresa[0]['remCp']);
        return $ListEmpresa[0];
    }



    /*******************************
     * getProv (cp)
     * Devuleve la provincia de un cp
     */
    function getProv($cp)
    {
        global $database;
        $aux = substr($cp, 0, 2);
        $q1 = "SELECT * from aux_provincias WHERE cod_pro='$aux'";
        $List = $database->getQuery($q1);
        $provincia = $List[0]['provincia'];
        return $provincia;
    }


    /*******************************
     * getServicios ()
     * Devuleve array con tipos de servicios para (empresa/cliente)
     */
    function getServicios()
    {
        global $database;
        $empresa_GLS_UID = $this->empresa_GLS_UID;
        $codcli = $this->codcli;

        $q = "SELECT * FROM aux_tipo_servicio WHERE empresa_GLS_UID='$empresa_GLS_UID' AND codcli='$codcli'";
        $serTotCount = $database->CuentaRegistros($q);

        #No hay servicios particulares, tiomar los globales
        if (!$serTotCount)  $q = "SELECT * FROM aux_tipo_servicio WHERE empresa_GLS_UID='*' AND codcli='*'";
        $q = $q . " ORDER BY codServicio  ";
        $serList = $database->getQuery($q);
        return ($serList);
    }


    /*******************************
     * getServicio (codServicio)
     * Devuleve los datos del tipo de servicio para una (empresa/cliente)
     */
    function getServicio($codServicio)
    {
        global $database;
        $empresa_GLS_UID = $this->empresa_GLS_UID;
        $codcli = $this->codcli;

        $q = "SELECT * FROM aux_tipo_servicio WHERE codServicio='$codServicio' AND empresa_GLS_UID='$empresa_GLS_UID' AND codcli='$codcli'";
        $serTotCount = $database->CuentaRegistros($q);

        #No hay servicios particulares, tiomar los globales
        if (!$serTotCount)  $q = "SELECT * FROM aux_tipo_servicio WHERE codServicio='$codServicio' AND empresa_GLS_UID='*' AND codcli='*'";

        $q = $q . " ORDER BY nombre ASC ";
        $serList = $database->getQuery($q);
        return ($serList[0]);
    }


    /*******************************
     * getHoraLimite ()
     * Devuleve la hora límite en la que se puede hacer un envío, en función de (empresa/cliente)
     */
    function getHoraLimite()
    {
        global $database;
        $empresa_GLS_UID = $this->empresa_GLS_UID;
        $codcli = $this->codcli;

        $q = "SELECT * FROM empresas WHERE empresa_GLS_UID='$empresa_GLS_UID' AND codcli='$codcli'";
        $list = $database->getQuery($q);
        $hora_limite_peticion = $list[0]['hora_limite_peticion'];
        return ($hora_limite_peticion);
    }



    /*******************************
     * getPaises()
     * Devuleve Array de paises
     */
    function getPaises($zona)
    {
        // ZONAS: 1- GLS Europa / 2-UPS Europa / 3-UPS Mundo
        if(!$zona) $zona="1";        
        global $database;
        $q = "SELECT * FROM paises WHERE zona$zona='1' ORDER BY nombre";
        $paisesList = $database->getQuery($q);
        return ($paisesList);
    }

    /*******************************
     * getPaisData()
     * Devuleve datos de un país
     */
    function getPaisData($iso)
    { 
        global $database;
        $q = "SELECT * FROM paises WHERE iso='$iso'";
        $paisesList = $database->getQuery($q);
        return ($paisesList[0]);
    }

    /*******************************
     * getErrGrabaservicios()
     * Devuleve descripción del código de error del WS grabaservicios 
     */
    function getErrGrabaservicios($cod)
    {
        global $database;
        $q = "SELECT * FROM aux_errores_grabaservicios WHERE codigo='$cod' ";
        $list = $database->getQuery($q);
        return ($list[0]['descripcion']);
    }
    

    /*******************************
     * getEnvio(codbar)
     * Devuleve los datos de un envío
     */
    function getEnvio($codbar)
    {
        global $database;
        $q = "SELECT * FROM envios WHERE codbar='$codbar' ";
        $list = $database->getQuery($q);
        return ($list[0]);
    }
}

$datos = new datos;
