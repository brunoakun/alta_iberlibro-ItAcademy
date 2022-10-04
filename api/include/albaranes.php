<?php

/**
 * Clase para la gestión de albaranes GLS y DTX
 * 11/05/2021 V1.0
 * 
 */


define("WS_UID", $_SESSION['empresa_GLS_UID']);

use setasign\Fpdi\Fpdi;

include_once("database.php");

class albaranes
{
    function __construct()
    {
        $this->time = time();
        $this->url = "https://wsclientes.asmred.com/b2b.asmx";
    }


    /*******************************
     * getUidExp (codbar)
     * Devuleve el uidexp de una expedición
     */
    function getUidExp($codbar)
    {
        global $database;
        $q = "SELECT * from expediciones WHERE codbar= '$codbar'";
        $expList = $database->getQuery($q);
        $uidExp = $expList[0]['uidExp'];
        return $uidExp;
    }



    /*******************************
     * getListDigitPropias (codbar)
     * Devuleve array de digitalizaciones propias
     */
    function getListDigitPropias($codbar)
    {
        global $database;
        $q = "SELECT * from digitalizaciones WHERE codbar= '$codbar'";
        $list = $database->getQuery($q);
        return $list;
    }




    /*******************************
     * Get datos de expedición (codigo de barras / albaran)
     */
    function getDataCodbar($codbar)
    {
        $metodo = "tem:WebServService___GetExpCli";
        $datosSOAP = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:asm="http://www.asmred.com/">
        <soap:Header/>
        <soap:Body>
           <asm:GetExpCli>
              <!--Optional:-->
              <asm:codigo>' . $codbar . '</asm:codigo>
              <!--Optional:-->
              <asm:uid>' . WS_UID . '</asm:uid>
           </asm:GetExpCli>
        </soap:Body>
     </soap:Envelope>';


        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);  // Array info 

        curl_close($ch);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new SimpleXMLElement($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        $doc = $array['soapBody']['GetExpCliResponse']['GetExpCliResult']['expediciones'];
        return $doc;
    }



    /*******************************
     * Get lista de trakings de expedición (codbar)
     */
    function getTrackListAlbaran($codbar)
    {
        $metodo = "tem:WebServService___GetExpCli";
        $datosSOAP = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:asm="http://www.asmred.com/">
        <soap:Header/>
        <soap:Body>
           <asm:GetExpCli>
              <!--Optional:-->
              <asm:codigo>' . $codbar . '</asm:codigo>
              <!--Optional:-->
              <asm:uid>' . WS_UID . '</asm:uid>
           </asm:GetExpCli>
        </soap:Body>
     </soap:Envelope>';


        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);  // Array info 

        curl_close($ch);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new SimpleXMLElement($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        $doc = $array['soapBody']['GetExpCliResponse']['GetExpCliResult']['expediciones']['exp']['tracking_list']['tracking'];
        return $doc;
    }




    /*******************************
     * Get lista de trakings de expedición (codbar)
     */
    function getImgExpediciones($codbar)
    {
        $uidExp = $this->getUidExp($codbar);
        $metodo = "tem:WebServService___GetExpCli";
        $datosSOAP = '     <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
        <soap12:Body>
          <GetExp xmlns="http://www.asmred.com/">
            <uid>' . $uidExp . '</uid>
          </GetExp>
        </soap12:Body>
      </soap12:Envelope>';

        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);  // Array info 

        curl_close($ch);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new SimpleXMLElement($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $aux = "";
        $doc = $array['soapBody']['GetExpResponse']['GetExpResult']['expediciones']['exp']['digitalizaciones'];
        return $doc;
    }




    /*******************************
     * Get lista de incidencias de expedición (codbar)
     * Odernado por fecha/Hora
     */
    function getInciList($codbar)
    {
        $trackList = $this->getTrackListAlbaran($codbar);
        $doc = array();
        foreach ($trackList as $track) {

            $fecha = $track['fecha'];
            $track['timestamp'] = 0;

            // Añadir "0" para horas de 1 solo dígito
            $aux = "";
            if (strlen($fecha) == 18) {
                $aux = substr($fecha, 0, 11);
                $aux .= "0";
                $aux .= substr($fecha, 11, 7);
                $track['fecha'] = $aux;
            }

            // Añadir el timeStamp de la Fecha/Hora
            if ($track['fecha']) {
                $timestamp = DateTime::createFromFormat('!d/m/Y H:i:s', $track['fecha'])->getTimestamp();
                $track['timestamp'] = $timestamp;
            }

            $tipo = $track['tipo'];
            if ($tipo == 'INCIDENCIA') array_push($doc, $track);
            if ($tipo == 'SOLUCION') array_push($doc, $track);
        }
        /*
        array_multisort(
            array_map('strtotime', array_column($doc, 'timestamp')),
            SORT_DESC,
            $doc
        );
        */
        if ($doc) {
            // Ordenar por el timeStamp de la Fecha/Hora
            $col = array_column($doc, "timestamp");
            array_multisort($col, SORT_ASC, $doc);
        }

        return $doc;
    }


    /*******************************
     * Get lista de estados/Inciedncias/Soluciones de expedición (codbar)     
     */
    function getEstadosList($codbar)
    {
        $trackList = $this->getTrackListAlbaran($codbar);
        $doc = array();
        foreach ($trackList as $track) {
            $fecha = $track['fecha'];
            $aux = "";
            if (strlen($fecha) == 18) {
                $aux = substr($fecha, 0, 11);
                $aux .= "0";
                $aux .= substr($fecha, 11, 7);
                $track['fecha'] = $aux;
            }
            $track['time'] = strtotime(str_replace("/", "-", $track['fecha']));

            $tipo = $track['tipo'];
            if ($tipo == 'INCIDENCIA') $track['evento'] = "INCIDENCIA - " . $track['evento'];
            if ($tipo == 'SOLUCION') $track['evento'] = "SOLUCION - " . $track['evento'];
            if ($tipo == 'ESTADO' || $tipo == 'INCIDENCIA' || $tipo == 'SOLUCION') array_push($doc, $track);
        }
        function array_sort_by_column(&$array, $column, $direction = SORT_ASC)
        {
            $reference_array = array();

            foreach ($array as $key => $row) {
                $reference_array[$key] = $row[$column];
            }

            array_multisort($reference_array, $direction, $array);
        }

        array_sort_by_column($doc, 'time');
        return $doc;
    }




    /******************************
     * Update datos de una expedición 
     * con datos devueltos por el WS GetExpCli AGRUPANDO UPDATES
     * V2.0 18/06/2021 (Agrupando Updates)
     */
    function SetDataCodbar($codbar)
    {
        global $database;
        $expedicion = $this->getDataCodbar($codbar);

        if ($expedicion) {
            foreach ($expedicion as $key => $testimonials) {
                $testimonials = (array) $testimonials;
                $flagGraba = false;
                $setVal = "";
                foreach ($testimonials as $key => $value) {
                    $quoted = true;
                    //if ($key != 'codbar') {

                    // strings
                    $value = trim($database->escape_str($value));

                    // decimal
                    if ($key == 'kgs' || $key == 'vol' || $key == 'kgsvol_prv_red_org' || $key == 'kgsvol_prv_red_dst' || $key == 'kgsvol_cli' || $key == 'kgsvol_prv' || $key == 'Reembolso') {
                        if (!$value) $value = 0;
                        $value = str_replace(',', '.', $value);
                        $value = round($value, 2);
                        $quoted = false;
                    }

                    // fechas a YYYY-mm-dd
                    if ($key == 'fecha' || $key == 'FPEntrega' || $key == 'fHoja' || $key == 'fPunteo') {
                        if ($value) {
                            $value = substr($value, 6, 4) . "-" . substr($value, 3, 2) . "-" . substr($value, 0, 2);
                        } else {
                            $quoted = false;
                            $value = "NULL";
                        }
                    }

                    // Valor "Quoted?"
                    if ($quoted) {
                        $setVal .= $key . "= '$value',";
                    } else {
                        $setVal .= $key . "= $value,";
                    }
                    //}
                    if ($key == 'Reembolso') $flagGraba = true;   // Último campo a grabar
                    if ($flagGraba) {
                        $setVal = trim($setVal, ',');
                        $q = "UPDATE expediciones SET $setVal WHERE codbar = '$codbar'";
                        $dbControl = $database->query($q);
                        if (!$dbControl) {
                            $msg = "ERROR intentando--> $q";
                            $database->log_msg(1, $GLOBALS['demarcacion'], $msg);
                            echo "$msg<br>";
                        }
                        break;
                    }
                }
            }

            $q = "UPDATE expediciones SET actualizadoEl = " . time() . " WHERE codbar = '$codbar'";
            $database->getQuery($q);
        }
    }



    /******************************
     * Update el ESTADO e INCIDENCIA de una expedición 
     * con datos devueltos por el WS GetExpCli
     * Si el estado es un estado final, marca la expedición con finalizadoEl
     */
    function SetEstadoCodbar($codbar)
    {
        global $database;
        $expedicion = $this->getDataCodbar($codbar);

        if ($expedicion) {
            $time = time();
            $codestado = $expedicion['exp']['codestado'];
            $estado = $expedicion['exp']['estado'];
            $incidencia = $expedicion['exp']['incidencia'];
            $q = "UPDATE expediciones SET codestado = '$codestado', estado='$estado', incidencia='$incidencia', actualizadoEl = $time WHERE codbar='$codbar'";
            $database->getQuery($q);

            // Mirar si es un estado final
            $q = "SELECT * FROM estados_finales WHERE estadoGLS='$codestado' ";
            $marcarEstadoFinal = $database->CuentaRegistros($q);
            if ($marcarEstadoFinal) {
                $q = "UPDATE expediciones SET finalizadoEl = $time WHERE codbar='$codbar'";
                $database->getQuery($q);
            }
        }
    }



    /******************************
     * Crea una solución para un albaran con incidencia
     * 
     */
    function grabaSolucion($codbar, $codSolucion, $obs)
    {
        global $database;
        $expedicion = $this->getDataCodbar($codbar);

        if ($expedicion) {
            $metodo = "tem:WebServService___GrabaSolucion";
            $datosSOAP = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:asm="http://www.asmred.com/">
            <soap:Header/>
            <soap:Body>
               <asm:GrabaSolucion>
                  <!--Optional:-->
                  <asm:uid>' . WS_UID . '</asm:uid>
                  <!--Optional:-->
                  <asm:codigo>' . $codbar . '</asm:codigo>
                  <asm:codSolucion>' . $codSolucion . '</asm:codSolucion>
                  <!--Optional:-->
                  <asm:observaciones>' . $obs . '</asm:observaciones>
               </asm:GrabaSolucion>
            </soap:Body>
         </soap:Envelope>';

            $headers = array(
                'Content-Type: text/xml; charset=utf-8',
                'Content-Length: ' . strlen($datosSOAP)
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            $responseInfo = curl_getinfo($ch);  // Array info 

            curl_close($ch);
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
            $xml = new SimpleXMLElement($response);
            $json = json_encode($xml);
            $array = json_decode($json, true);

            $doc = $array['soapBody']['GrabaSolucionResponse']['GrabaSolucionResult'];

            return $doc;
        }
    }




    /*******************************
     * Get getPlaza (cp)
     * Devuleve los datos de la AGENCIA de un CP
     */
    function getPlaza($cp)
    {
        global $database;
        if (!$cp) return (false);

        $metodo = "tem:WebServService___GetPlazaXCP";
        $datosSOAP = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:asm="http://www.asmred.com/">
        <soapenv:Header/>
        <soapenv:Body>
           <asm:GetPlazaXCP>
              <asm:codPais>34</asm:codPais>
              <!--Optional:-->
              <asm:cp>' . $cp . '</asm:cp>
           </asm:GetPlazaXCP>
        </soapenv:Body>
     </soapenv:Envelope>';

        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);  // Array info 

        curl_close($ch);
        if ($result) {
            $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
            $xml = new SimpleXMLElement($response);
            $json = json_encode($xml);
            $array = json_decode($json, true);

            $doc = $array['soapBody']['GetPlazaXCPResponse']['GetPlazaXCPResult']['Plaza'];
        } else {
            $aux = $_SERVER['PHP_SELF'];
            $msg = "Error CRON $aux Error->$metodo sin respuesta del SERVER llamada GetPlazaXCP ";
            $database->log_msg(1, $GLOBALS['demarcacion'], $msg);
            $GLOBALS['errorLog'] = true;
        }

        if (!$doc) return false;
        return $doc;
    }


    /*******************************
     * Get getEstadosFinales ()
     * Devuleve array con los CODIGOS de estados finales GLS
     */
    function getEstadosFinales()
    {
        global $database;
        $doc = array();
        $q = "SELECT * FROM estados_finales";
        $estadosFinalesList = $database->getQuery($q);
        if ($estadosFinalesList) {
            foreach ($estadosFinalesList as $rec) {
                array_push($doc, $rec['estadoGLS']);
            }
        }
        return $doc;
    }


    /*******************************
     * Get getSms(codbar)
     * Devuleve array con los SMS enviados a una expedición
     */
    function getSms($codbar)
    {
        global $database;
        $doc = array();
        $q = "SELECT * FROM sms_envios WHERE codbar='$codbar'";
        $doc = $database->getQuery($q);
        return $doc;
    }




    /*******************************
     * Get lista de bultos de expedición (codbar)
     */
    function getBultosList($codbar)
    {
        $bultosList = $this->getDetalleBultosAlbaran($codbar);
        if (!$bultosList) return null;

        $doc = array();
        $bultosTot = 0;
        $volumenTot = 0;
        $kilosTot = 0;

        # Varios bultos
        if ($bultosList[0]['bulto'] != null) {
            foreach ($bultosList as $paquete) {
                $bultosTot++;

                $kilos = $this->toNum($paquete['kilos']);
                $kilosTot = $kilosTot + $kilos;

                $largo = $this->toNum($paquete['largo']);
                $ancho = $this->toNum($paquete['ancho']);
                $alto = $this->toNum($paquete['alto']);
                $volumen = (($largo * $ancho * $alto) / 1000000);
                $volumenTot = $volumenTot + $volumen;
            }
        } else {
            # 1 bulto
            $bultosTot = 1;

            $kilos = $this->toNum($bultosList['kilos']);
            $kilosTot = $kilosTot + $kilos;

            $largo = $this->toNum($bultosList['largo']);
            $ancho = $this->toNum($bultosList['ancho']);
            $alto = $this->toNum($bultosList['alto']);
            $volumen = (($largo * $ancho * $alto) / 1000000);
            $volumenTot = $volumenTot + $volumen;
        }
        if (!$bultosList) {
            $doc = null;
        } else {
            # calcular el lado del cubo imaginario
            $lado = pow($volumenTot, 1 / 3); // Elevar número a la potencia 0.333333333
            $lado = $lado * 100;   // Pasarlo a centímetros

            # Montar array de salida
            array_push($doc['bultosTot'] = $bultosTot);
            array_push($doc['kilosTot'] = $kilosTot);
            array_push($doc['volumenTot'] = $volumenTot);
            array_push($doc['largo'] = $lado);
            array_push($doc['ancho'] = $lado);
            array_push($doc['alto'] = $lado);
        }
        return $doc;
    }


    function toNum($str)
    {
        return str_replace(",", ".", $str);
    }






    /*******************************
     * Get lista de bultos de una expedición (codbar)
     */
    function getDetalleBultosAlbaran($codbar)
    {
        $WS_GLS_UID = $this->getWS_GLS_UID($codbar);
        $metodo = "tem:WebServService___GetExpCli";
        $datosSOAP = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:asm="http://www.asmred.com/">
        <soap:Header/>
        <soap:Body>
           <asm:GetExpCli>
              <asm:codigo>' . $codbar . '</asm:codigo>
              <asm:uid>' . $WS_GLS_UID . '</asm:uid>
           </asm:GetExpCli>
        </soap:Body>
     </soap:Envelope>';


        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);  // Array info 

        curl_close($ch);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new SimpleXMLElement($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        $doc = $array['soapBody']['GetExpCliResponse']['GetExpCliResult']['expediciones']['exp']['detallebultos']['bulto'];

        // Control 1 solo registro
        if ($doc[0]['bulto'] == null) {
            $aux =  array();
            array_push($aux, $doc);
            $doc = $aux;
        }


        return $doc;
    }





    /*******************************
     * Get WS_GLS_UID (codbar)
     * Devuleve UID el cliente de una expedición
     */
    function getWS_GLS_UID($codbar)
    {
        global $database;
        $q = "SELECT * from expediciones WHERE codbar= '$codbar'";      //61103081150355
        $expList = $database->getQuery($q);
        $UIDcli = $expList[0]['UIDcli'];
        return $UIDcli;
    }




    /*******************************
     * GetExpGls (codbar)
     * Devuleve array de datos de una expedición en GLS
     */
    function GetExpGls($codbar)
    {
        global $database;
        $q = "SELECT * from expediciones WHERE codbar= '$codbar'";
        $expList = $database->getQuery($q);
        return $expList;
    }






    /*******************************
     * getEtiquetas(codbar)
     * Devuleve array con las etiquetas de un envío
     */
    function getEtiquetas($codbar)
    {
        $url           = "https://wsclientes.asmred.com/b2b.asmx";
        $metodo        = "EtiquetaEnvio";

        $datosSOAP = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:asm="http://www.asmred.com/">
        <soap:Header/>
        <soap:Body>
           <asm:EtiquetaEnvio>
              <!--Optional:-->
              <asm:codigo>' . $codbar . '</asm:codigo>
              <asm:tipoEtiqueta>PDF</asm:tipoEtiqueta>
           </asm:EtiquetaEnvio>
        </soap:Body>
        </soap:Envelope>';


        $fp = fopen('datosSOAP.txt', 'w');
        fwrite($fp, $datosSOAP);
        fclose($fp);


        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);

        // No hay resultado desde el WS, marcar error en esta línea
        if (($result) === FALSE) {
            $ws_err = 1;
            $ws_errMsg = "Error en la llamada al ws $metodo: " . curl_error($ch);
        } else {

            ///////////// Array info ////////    
            $responseInfo = curl_getinfo($ch);
        }
        curl_close($ch);

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        $xml = new SimpleXMLElement($response);

        // convertir el XML a un array para recorrerlo
        $json = json_encode($xml);
        $array = json_decode($json, true);

        $resData = $array['soapBody']['EtiquetaEnvioResponse']['EtiquetaEnvioResult'];

        $ws_errMsg  = $resData['Errores']['Error'];
        $ws_etiqueta  = $resData['base64Binary'];


        $respuesta = array();
        $respuesta["etiquetas"] = $ws_etiqueta;
        $respuesta["info"] = $responseInfo;
        $respuesta["error"] = $ws_errMsg;

        return ($respuesta);
    }





    /*******************************
     * addLogoLabel($file, $empresa_logo_label) 
     * Añade el logo a una etiqueta de envío
     */

    function addLogoLabel($pdfFile, $logo)
    {
        //    use setasign\Fpdi\Fpdi;  <-- Fuera de la función, definido en la clase

        require_once('fpdf/fpdf.php');
        require_once('fpdi/src/autoload.php');
        require_once('fpdi/src/Fpdi.php');

        // Config
        // $pdfFile = '../data/pdf/work/doc02.pdf';
        // $logo = '../data/logos/iberlibro_logo_label.jpg';
        $x = 30;
        $y = 108;
        $ancho = 50;
        $alto = 25;

        $pdf = new Fpdi();
        $totPaginas = $pdf->setSourceFile($pdfFile);
        for ($pagina = 1; $pagina <= $totPaginas; $pagina++) {
            $template = $pdf->importPage($pagina);
            $pdf->AddPage();
            $pdf->useTemplate($template, 0, 0, null, null, true);
            $pdf->Image($logo, $x, $y, $ancho);
        }

        // pdfOutputMethod "I" -> Browser Inline, "D" -> Force Download, "F" -> Save to Disk
        $pdf->Output('F', $pdfFile);
    }

    /**********************************************
     * 
     * 
     *                    DTX
     * 
     * 
     **********************************************/





    /******************************
     * Devuelve valores para autenticar en DTX
     * Si no existe devuelve false
     */
    function getAuthDtx($WS_GLS_CODIGO)
    {
        global $database;
        $q = "SELECT * FROM agencias WHERE WS_GLS_CODIGO ='$WS_GLS_CODIGO'";
        $aux = $database->getQuery($q);
        if (!$aux) {
            $msg = "Fin     " . date("Y/m/d H:i:s") . " getAuthDtx(), Error-> no se ha encontrado ninguna agencia con WS_GLS_CODIGO: $WS_GLS_CODIGO ";
            $database->log_msg(LOG_ERR_CRON, '', $msg);
            return false;
        }
        $WS_DTX_IDENTIFICADOR = $aux[0]['WS_DTX_IDENTIFICADOR'];
        $WS_DTX_EMPRESA = $aux[0]['WS_DTX_EMPRESA'];
        $WS_DTX_AGENCIA = $aux[0]['WS_DTX_AGENCIA'];
        $WS_DTX_USUARIO = $aux[0]['WS_DTX_USUARIO'];
        $WS_DTX_CLAVE = $aux[0]['WS_DTX_CLAVE'];

        $dtxAuth = array();
        $dtxAuth["identificador"] = $WS_DTX_IDENTIFICADOR;
        $dtxAuth["empresa"] = $WS_DTX_EMPRESA;
        $dtxAuth["agencia"] = $WS_DTX_AGENCIA;
        $dtxAuth["usuario"] = $WS_DTX_USUARIO;
        $dtxAuth["clave"] = $WS_DTX_CLAVE;

        return $dtxAuth;
    }



    /*******************************
     * Get datos de expedición (dtxAuth[], albaran)
     */

    function getDatosDtx($dtxAuth, $albaran)
    {
        global $database;
        if (!$albaran) return (false);
        $url = "http://dtxagesw.datatransdtx.com:8081/dtxAgeSW/services/EnviosDtxAge";
        $metodo = "tem:WebServService___EnviosDtxAge";
        $datosSOAP = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mes="http://messagein.dtx.sw" xmlns:com="http://complexType.dtx.sw">
        <soapenv:Header/>
        <soapenv:Body>
           <mes:ConsultarEnviosTypeIn>
              <mes:autenticacion>
                 <com:identificador>' . $dtxAuth['identificador'] . '</com:identificador>
                 <com:empresa>' . $dtxAuth['empresa'] . '</com:empresa>
                 <com:agencia>' . $dtxAuth['agencia'] . '</com:agencia>
                 <com:usuario>' . $dtxAuth['usuario'] . '</com:usuario>
                 <com:clave>' . $dtxAuth['clave'] . '</com:clave>
              </mes:autenticacion>
              <mes:albaran>' . $albaran . '</mes:albaran>
              <mes:referencia></mes:referencia>
              <mes:idRuta></mes:idRuta>
           </mes:ConsultarEnviosTypeIn>
        </soapenv:Body>
     </soapenv:Envelope>';


        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($datosSOAP)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datosSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);  // Array info 

        curl_close($ch);
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
        if ($response) $xml = new SimpleXMLElement($response);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        $doc = $array['soapenvBody']['ns4ConsultarEnviosTypeOut']['ns4resultado'];
        $resCodError = $array['soapenvBody']['ns4ConsultarEnviosTypeOut']['ns4codError'];
        $resMsgError = $array['soapenvBody']['ns4ConsultarEnviosTypeOut']['ns4msgError'];

        if ($resCodError && $resCodError <> "200") {
            //  echo "Código de Error: $resCodError - $resMsgError Albaran: $albaran<br>";
            $database->log_msg(LOG_CRON,  $GLOBALS['demarcacion'], "ERROR webService $metodo -> Error: $resCodError - $resMsgError Albaran: $albaran");
        }

        if ($resCodError) {
            $doc['codError']  = $resCodError;
            $doc['msgError']  = $resMsgError;
        }
        // if (!$doc) return false;
        return $doc;
    }






    /*********************************************
     * Devuelve datos de una expedicion desde DTX
     * getRefDtx(codbar)
     *********************************************/
    function getRefDtx($codbar)
    {
        global $database;
        if (!$codbar) return (false);

        $expList = $this->GetExpGls($codbar);
        $WS_GLS_CODIGO = $expList[0]['codplaza_pag'];
        $albaran = $expList[0]['albaran'];
        $dtxAuth = $this->getAuthDtx($WS_GLS_CODIGO);
        $arrInfo = $this->getDatosDtx($dtxAuth, $albaran);
        return ($arrInfo);
    }
}



$albaranes = new albaranes;
