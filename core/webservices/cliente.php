<?php
use classes\Meter;
use classes\KaviAction;
use classes\AlarmsMeters;
require_once '../libs/loader.php';
\Loader::classLoader();
define("PROD", "PROD");
if (defined("PROD")) {
    define('IP', "10.4.12.8");
    define('USER', "Pr0tcl0ud");
    define('PASS', "p455pr073c54");
    define('ID_APP', 3); 
} else {
    define('IP', "10.4.57.48");
    define('USER', "PR073C54");
    define('PASS', "PR070");
    define('ID_APP', 21);
}
date_default_timezone_set('America/Mexico_City');
$meter = new Meter();

/******START SOAP 1.2 **********/
$ns = 'http://www.cfe.gob.mx/';
$headerbody = array(
    'Usuario' => USER,
    'Clave' => PASS
);
$header = new SOAPHeader($ns, 'AuthHeader', $headerbody);
$options = array(
    'soap_version' => SOAP_1_2,
    'encoding' => 'UTF-8',
    'trace' => 1,
    'exceptions' => true,
    'cache_wsdl' => WSDL_CACHE_NONE
);
try {
    $client = new \SoapClient("http://".IP."/wsKaviCentinel/wsKaviCentinel.asmx?wsdl", $options);
    $client->__setSoapHeaders($header);
} catch (Exception $e) {
    
}
/******END SOAP 1.2 **********/

while (true) {
    $metersUnregisted = $meter->lists(- 1, - 1, true, "registered='false'");
    if(false == isset($metersUnregisted[0])){
        if(true ==!empty($metersUnregisted)){
            $metersUnregisted = array($metersUnregisted);
        }
    }
    foreach ($metersUnregisted as $item) {
        $meter->get((int) ($item['idmeters']));
        $meter->getConsumptions();
        $meter->getStatus_meter();
        $state = '';
        $status = 'Activo';
        $meter->status_meter->state_relay == 'close' ? $state = 'Cerrado' : $state = 'Abierto';
        $meter->status == 'enabled' ? $status = 'Activo' : $status = 'Inactivo';
        $date = date("Y-m-d") . "T" . date("H:i:s");
        $params = array(
            "datosMedidor" => array(
                "Medidor" => array(
                    "NumeroMedidor" => $item['serial_number']
                ),
                "CodigoMedidor" => $meter->code_meter,
                "LoteMedidor" => $meter->lote_code,
                "Marca" => $meter->idVendor,
                "Modelo" => $meter->model_meter,
                "VersionFirmware" => "1.3.1",
                "Latitud" => $meter->latitud,
                "Longitud" => $meter->longitud,
                "EstadoRelevador" => $state,
                "EstadoMedidor" => $status,
                "FechaHoraRegistro" => $date,
                "ListaLecturas" => array(
                    "Lectura" => array(
                        "Tipo" => "ConsumoTotal",
                        "Valor" => $meter->consumptions->total_kwh_balance
                    )
                )
            ),
            "idSigAmi" => ID_APP
        );
        if (false == isset($client) ) {
            continue;
        }
        if (NULL == $client ) {
            continue;
        }
        $response = $client->__soapCall("RegistrarNuevoMedidor", array(
            $params
        ));
        $resp = strtolower($response->RegistrarNuevoMedidorResult->Respuesta);
        echo "<pre>";
        var_dump($resp);
        echo "</pre>";
        if ( $resp == 'ok' ) {
            $meter->edit($meter->idmeters,array('registered'=>'true'));
        }
    }
    $kaviAction = new KaviAction();
    $arrayUnconmpleted = array();
    $arrayNotSending = array();
    $actionincomplete = $kaviAction->lists(-1,-1,'uncompleted');
    if(isset($actionincomplete[0]) == false ){
        $actionincomplete = array($actionincomplete);
    }
    /* if($actionincomplete[0] == null){
        $actionincomplete = array($actionincomplete);
    } */
    foreach ($actionincomplete as $action) {
        if( isset($action['idkavi_action']) == false ){
            continue;
        }
        $kaviAction->get((int)$action['idkavi_action']);
        /* echo "<pre>";
        var_dump($action);
        echo "</pre>"; */
        if(
            $kaviAction->type_action=='ConsumoDiurno' || $kaviAction->type_action=='ConsumoNocturno' ||
            $kaviAction->type_action=='ConsumoEnergiaEntregada' || 
            $kaviAction->type_action=='ConsumoEnergiaRecibida' ||
            $kaviAction->type_action=='DemandaMaxima' || $kaviAction->type_action=='ConsumoTotal' ||
            $kaviAction->type_action=='ConsumoFase1' || $kaviAction->type_action=='ConsumoFase2' ||
            $kaviAction->type_action=='ConsumoFase3' || $kaviAction->type_action=='Reactivos' 
            ){
            $meter->get($kaviAction->id_meter);
            
            if($meter->idmeters<1){
                continue;
            }
            $meter->getConsumptions();
            $meter->getStatus_meter();
            $value = -1;
            switch ($kaviAction->type_action) {
                case 'ConsumoDiurno':
                break;
                case 'ConsumoNocturno':
                    break;
                case 'ConsumoEnergiaEntregada':
                    $value = $meter->consumptions->total_kwh_delivered;
                    break;
                case 'ConsumoEnergiaRecibida':
                    $value = $meter->consumptions->total_kwh_recieved;
                    break;
                case 'DemandaMaxima':
                    
                    break;
                case 'Reactivos':
                    $value = $meter->consumptions->kvar_I_total;
                    break;
                default:
                    $value = $meter->consumptions->total_kwh_balance;
                break;
            }
            $date1 = str_replace(" ", "T", $kaviAction->init_request);
            $date2 = date('Y-m-d')."T".date("H:i:s");
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['init_timestamp'] =$date1;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['end_timestamp'] =$date2;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['relay'] =$meter->status_meter->state_relay;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['status'] =$meter->status;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['reading'][] = array(
                'Tipo' => $kaviAction->type_action,
                'Valor' => $value*1000
            );
        }else if( $kaviAction->type_action =='info' ){
            $isinfo = true;
            $meter->get($kaviAction->id_meter);
            if($meter->idmeters>0){
                $meter->getConsumptions();
            }
            $stateMEter = 'Enabled';
            if(null!=$kaviAction->parametres){
                $kaviAction->parametres = json_decode($kaviAction->parametres);
                $kaviAction->parametres->EstadoMedidor == 'Activo'?$stateMEter = 'Enabled':$stateMEter = 'Disabled';
                if( '' == $kaviAction->parametres->CodigoMedidor || null != $kaviAction->parametres->CodigoMedidor ){
                    $status = $meter->edit($meter->idmeters, array(
                        'code_meter' => $kaviAction->parametres->CodigoMedidor,
                        'lote_code' => $kaviAction->parametres->LoteMedidor,
                        'status' => $stateMEter
                    ));
                    if($status > -1 ){
                        $status='OK';
                    }else{
                        $status='ERROR';
                    }
                    $arrayNotSending[$kaviAction->uuid_cfe]['TYPE'] = 'info';
                    $arrayNotSending[$kaviAction->uuid_cfe]['listaConfirmaciones'][] = array(
                        'ConfirmacionActualizacionDatosMedidor' => array(
                            'Medidor' => array(
                                'NumeroMedidor' => $meter->serial_number
                            ),
                            'ResultadoActualizacion'=>$status,
                            'CodigoErrorActualizacion'=>'Ninguno',
                            'DescripcionErrorActualizacion'=>'',
                            'FechaActualizacion'=>date("Y-m-d")."T".date('H:i:s'),
                        )
                    );
                }else{
                    $arrayNotSending[$kaviAction->uuid_cfe]['TYPE'] = 'get_info';
                    $arrayNotSending[$kaviAction->uuid_cfe]['listaConfirmaciones'][] = array(
                        'listaDatosMedidor' => array(
                            'Medidor' => array(
                                'NumeroMedidor' => $kaviAction->parametres[0]
                            ),
                            'CodigoMedidor'=>"Desconocido",
                            'LoteMedidor'=>"Desconocido",
                            'Marca'=>"Desconocido",
                            'Modelo'=>"Desconocido",
                            'VersionFirmware'=>"Desconocido",
                            'Latitud'=>0,
                            'Longitud'=>0,
                            'EstadoRelevador'=>"Desconocido",
                            'EstadoMedidor'=>"Inactivo",
                            'FechaHoraRegistro'=>date("Y-m-d")."T".date('H:i:s'),
                            'Lecturas'=>array()
                        )
                    );
                }
            }else{
                $arrayNotSending[$kaviAction->uuid_cfe]['TYPE'] = 'get_info';
                $relay = 'Cerrado';
                $stMeter = 'Activo';
                $arrayNotSending[$kaviAction->uuid_cfe]['listaConfirmaciones'][] = array(
                    'listaDatosMedidor' => array(
                        'Medidor' => array(
                            'NumeroMedidor' => $meter->serial_number
                        ),
                        'CodigoMedidor'=>$meter->code_meter,
                        'LoteMedidor'=>$meter->lote_code,
                        'Marca'=>$meter->idVendor,
                        'Modelo'=>$meter->model_meter,
                        'VersionFirmware'=>'1.3.1',
                        'Latitud'=>$meter->latitud,
                        'Longitud'=>$meter->longitud,
                        'EstadoRelevador'=>$relay,
                        'EstadoMedidor'=>$stMeter,
                        'FechaHoraRegistro'=>date("Y-m-d")."T".date('H:i:s'),
                        'Lecturas'=>array()
                    )
                );
            }
        }else if($kaviAction->type_action == 'alerts' ){
            if (null!=$kaviAction->parametres) {
                $meter->get($kaviAction->id_meter);
                if($meter->idmeters<1){
                    continue;
                }
                $kaviAction->parametres = json_decode($kaviAction->parametres);
                $kaviAction->parametres->start_date = str_replace("T", " ", $kaviAction->parametres->start_date);
                $kaviAction->parametres->end_date = str_replace("T", " ", $kaviAction->parametres->end_date);
                $meter->getConsumptions();
                $arrayReads = array();
                if(null!=$kaviAction->parametres->reading){
                    foreach ($kaviAction->parametres->reading as $val) {
                        $value = 0;
                        switch ($val) {
                            case 'ConsumoDiurno':
                            break;
                            case 'ConsumoNocturno':
                                break;
                            case 'ConsumoEnergiaEntregada':
                                $value = $meter->consumptions->total_kwh_delivered;
                                break;
                            case 'ConsumoEnergiaRecibida':
                                $value = $meter->consumptions->total_kwh_recieved;
                                break;
                            case 'DemandaMaxima':
                                
                                break;
                            case 'Reactivos':
                                $value = $meter->consumptions->kvar_I_total;
                                break;
                            default:
                                $value = $meter->consumptions->total_kwh_balance;
                            break;
                        }
                        $arrayReads[] = array(
                            'Tipo' => $val,
                            'Valor' => $value*1000
                        );
                    }
                }
            }
            $meter->getStatus_meter();
            $arrayNotSending[$kaviAction->uuid_cfe]['TYPE'] = 'alerts';
            $arrayNotSending['resultadoExtraerEventosMedidor']['Medidor']['NumeroMedidor'] = $meter->serial_number;
            $arrayNotSending['resultadoExtraerEventosMedidor']['ResultadoExtraccionEventos'] = 'OK';
            $arrayNotSending['resultadoExtraerEventosMedidor']['CodigoErrorExtraccionEventos'] = 'Ninguno';
            $arrayNotSending['resultadoExtraerEventosMedidor']['DescripcionErrorExtraccionEventos'] = '';
            $arrayNotSending['resultadoExtraerEventosMedidor']['FechaInicioExtraccionEventos'] = date('Y-m-d')."T".date("H:i:s");
            $arrayNotSending['resultadoExtraerEventosMedidor']['FechaFinExtraccionEventos'] = date('Y-m-d')."T".date("H:i:s");
            $status = 'Activo';
            $state = 'Cerrado';
            $meter->status_meter->state_relay == 'close' ? $state = 'Cerrado' : $state = 'Abierto';
            $meter->status == 'enabled' ? $status = 'Activo' : $status = 'Inactivo';
            $arrayNotSending['resultadoExtraerEventosMedidor']['EstadoRelevador'] = $state;
            $arrayNotSending['resultadoExtraerEventosMedidor']['EstadoMedidor'] = $status;
            $alerts = new AlarmsMeters();
            $alerts->get($meter->idmeters,$kaviAction->parametres->start_date,$kaviAction->parametres->end_date);
            $index = 0;
            foreach ($alerts->alarms as $alert) {
                $code = -1;
                switch ($alert['id_type_event']) {
                    case 118:
                        $code = 2;
                    break;
                    case 119:
                        $code = 3;
                        break;
                    case 125:
                        $code = 4;
                        break;
                    case 129:
                        $code = 5;
                        break;
                    case 112:
                        $code = 9;
                        break;
                        
                }
                if($code > 0){
                    $arrayNotSending['resultadoExtraerEventosMedidor']['ListaEventosMedidor'][] = array(
                     'EventoMedidor' => array(
                     'CodigoEventoCFE' => $code,
                     'CodigoEventoSigAmi' => $alert['id_type_event'],
                     'DescripcionEventoSigAmi' => $alert['description'],
                     'FechaEventoSigAmi' => $alert['timestamp_meter'],
                     )
                    );
                }
            }
            $arrayNotSending['resultadoExtraerEventosMedidor']['ListaLecturas'] = $arrayReads;
        }
    }
    $actionsWaits = array();
    /* var_dump($arrayNotSending);
    exit(1); */
    foreach ($arrayNotSending as $k => $v) {
        $arrr = array(
            'idPeticion' => $k
        );
        unset($sending);
        if(NULL!=$arrayNotSending[$k]['TYPE']){
            $sending['idPeticion'] = $k;
            if($arrayNotSending[$k]['TYPE']=='info'){
                unset($arrayNotSending[$k]['TYPE']);
                foreach ($arrayNotSending[$k]['listaConfirmaciones'] as $r){
                    $sending['listaConfirmaciones'][] = $r['ConfirmacionActualizacionDatosMedidor'];
                }
                if(null!=$client)
                {
                    try {
                        $response = $client->__soapCall("RecibirConfirmacionActualizacionDatosMedidores", array(
                            $sending
                        ));
                        $res = strtolower($response->RecibirConfirmacionActualizacionDatosMedidoresResult->Respuesta);
                        if( $res == "ok" || $res == "OK" ){
                            $update = $kaviAction->edit((string)$sending['idPeticion'],array('status'=>'success'));
                        }
                    } catch (Exception $e) {
                        echo '<H1>REQUEST</H1><br><pre>', htmlentities($client->__getLastRequest()) ,
                        '</pre><br/><br/><H1>ERROR MESSAGE</H1><br/>',$e->getMessage();
                        echo "<pre>";
                        var_dump($sending);
                        echo "<pre>";
                    }
                }    
            }if($arrayNotSending[$k]['TYPE']=='alerts'){
                $sending['idPeticion'] = $k;
                $sending[] = $arrayNotSending['resultadoExtraerEventosMedidor'];
                echo "<pre>";
                var_dump($sending);
                echo "<pre>";
                if(null!=$client)
                {
                    try {
                        $response = $client->__soapCall("RecibirEventos", array($sending));
                        $res = strtolower($response->RecibirEventosResult->Respuesta);
                        if( $res == "ok" || $res == "OK" ){
                            $update = $kaviAction->edit((string)$sending['idPeticion'],array('status'=>'success'));
                        }
                    } catch (Exception $e) {
                        echo '<H1>REQUEST</H1><br><pre>', htmlentities($client->__getLastRequest()) ,
                        '</pre><br/><br/><H1>ERROR MESSAGE</H1><br/>',$e->getMessage();
                        echo "<pre>";
                        var_dump($sending);
                        echo "<pre>";
                    }
                }
            }else{
                unset($arrayNotSending[$k]['TYPE']);
                foreach ($arrayNotSending[$k]['listaConfirmaciones'] as $r){
                    $sending['listaDatosMedidor'][] = $r['listaDatosMedidor'];
                }
                if(null!=$client)
                {
                    try {
                        $response = $client->__soapCall("RecibirDatosMedidores", array(
                            $sending
                        ));
                        $res = strtolower($response->RecibirDatosMedidoresResult->Respuesta);
                        if( $res == "ok" || $res == "OK" ){
                            $update = $kaviAction->edit((string)$sending['idPeticion'],array('status'=>'success'));
                        }
                    } catch (Exception $e) {
                        echo '<H1>REQUEST</H1><br><pre>', htmlentities($client->__getLastRequest()) ,
                        '</pre><br/><br/><H1>ERROR MESSAGE</H1><br/>',$e->getMessage();
                        echo "<pre>";
                        var_dump($sending);
                        echo "<pre>";
                    }
                }
            }
            continue;
        }
        foreach ($v as $kk => $m) {
            $relay = '';
            $status = '';
            $m['relay'] == 'close' ? $relay = 'Cerrado' : $relay = 'Abierto';
            $m['status'] == 'enabled' ? $status = 'Activo' : $status = 'Inactivo'; 
            $arrr['listaResultados'][] = array('Medidor' => array(
                    'NumeroMedidor' => $kk
                ),
                'ResultadoOperacion' => 'OK',
                'CodigoErrorLectura' => 'Ninguno',
                'DescripcionErrorLectura' => '',
                'FechaInicioLectura' => $m['init_timestamp'],
                'FechaFinLectura' => $m['end_timestamp'],
                'EstadoRelevador' => $relay,
                'EstadoMedidor' => $status,
                'ListaLecturas' => $m['reading']
            );
        }
        $actionsWaits[]=$arrr;
    }
    
    /* echo "<pre>";
    var_dump($actionsWaits);
    echo "</pre>";
    exit();  */
   
    print "<pre>";
    foreach ($actionsWaits as $tckt) {
        if(null!=$client)
        {
            try {
                $response = $client->__soapCall("RecibirLecturas", array(
                    $tckt
                ));
                var_dump($response);
                $res = strtolower($response->RecibirLecturasResult->Respuesta);
                if( $res == "ok" || $res == "OK" ){
                    $update = $kaviAction->edit((string)$tckt['idPeticion'],array('status'=>'success'));
                }    
            } catch (Exception $e) {
                var_dump($e);
            }
               
        }
    }
    $arrayUnconmpleted = array();
    $arrayType = array();
    $arrayNotSending = array();
    $actionNotSending = $kaviAction->lists(-1,-1,'notSending');
    sleep(40);
    $actionNotSending = $kaviAction->lists(-1,-1,'notSending');
    if(false == isset($actionNotSending[0])){
        $actionNotSending = array($actionNotSending);
    }
    /* if($actionNotSending[0] == null){
        $actionNotSending = array($actionNotSending);
    } */
    foreach ($actionNotSending as $action) {
        if( false == isset($action['idkavi_action']) )
            continue;
        $kaviAction->get((int)$action['idkavi_action']);
        $kaviAction->parametres = json_decode($kaviAction->parametres);
        if(
            $kaviAction->type_action=='reading' || $kaviAction->type_action=='sync' ||
            $kaviAction->type_action=='relay_off' ||
            $kaviAction->type_action=='relay_on' ||
            $kaviAction->type_action=='info' || $kaviAction->type_action=='alerts'
        ){
            $meter->get($kaviAction->id_meter);
            if($meter->idmeters<1){
                continue;
            }
            $meter->getConsumptions();
            $meter->getStatus_meter();
            $value = -1;
            $arrayReading = array();
            foreach ($kaviAction->parametres as $typeRead) {
                switch ($typeRead) {
                    case 'ConsumoDiurno':
                        break;
                    case 'ConsumoNocturno':
                        break;
                    case 'ConsumoEnergiaEntregada':
                        $value = $meter->consumptions->total_kwh_delivered;
                        break;
                    case 'ConsumoEnergiaRecibida':
                        $value = $meter->consumptions->total_kwh_recieved;
                        break;
                    case 'DemandaMaxima':
        
                        break;
                    case 'Reactivos':
                        $value = $meter->consumptions->kvar_I_total;
                        break;
                    default:
                        $value = $meter->consumptions->total_kwh_balance;
                        $arrayReading[]= array(
                                'Tipo' => $typeRead,
                                'Valor' => $value*1000
                        );
                        break;
                }
            }
            $date1 = str_replace(" ", "T", $kaviAction->init_request);
            $date2 = date('Y-m-d')."T".date("H:i:s");
            $arrayType[$kaviAction->uuid_cfe] = $kaviAction->type_action;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['init_timestamp'] =$date1;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['end_timestamp'] =$date2;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['relay'] =$meter->status_meter->state_relay;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['status'] =$meter->status;
            $arrayNotSending[$kaviAction->uuid_cfe][$meter->serial_number]['reading'] = $arrayReading;
        }
    }
    $actionsWaits = array();
    /***/
    foreach ($arrayNotSending as $k => $v) {
        $arrr = array(
            'idPeticion' => $k
        );
        foreach ($v as $kk => $m) {
            $mtr = new Meter();
            $mtr->get($kk);
            $mtr->getStatus_meter();
            $relay = '';
            $status = '';
            $mtr->status_meter->state_relay == 'close' ? $relay = 'Cerrado' : $relay = 'Abierto';
            $mtr->status == 'enabled' ? $status = 'Activo' : $status = 'Inactivo';
            switch ($arrayType[$k]) {
                case 'relay_off':
                    $arrr['listaResultados'][] = array('Medidor' => array(
                        'NumeroMedidor' => $kk
                    ),
                        'ResultadoDesconexion' => 'OK',
                        'CodigoErrorDesconexion' => 'Ninguno',
                        'DescripcionErrorDesconexion' => '',
                        'FechaInicioDesconexion' => $m['init_timestamp'],
                        'FechaFinDesconexion' => $m['end_timestamp'],
                        'EstadoRelevador' => $relay,
                        'EstadoMedidor' => $status,
                        'ListaLecturas' => $m['reading']
                    );
                break;
                case 'relay_on':
                    $arrr['listaResultados'][] = array('Medidor' => array(
                        'NumeroMedidor' => $kk
                    ),
                        'ResultadoReconexion' => 'OK',
                        'CodigoErrorReconexion' => 'Ninguno',
                        'DescripcionErrorReconexion' => '',
                        'FechaInicioReconexion' => $m['init_timestamp'],
                        'FechaFinReconexion' => $m['end_timestamp'],
                        'EstadoRelevador' => $relay,
                        'EstadoMedidor' => $status,
                        'ListaLecturas' => $m['reading']
                    );
                    break;
            }
            
        }
        $actionsWaits[]=$arrr;
    }
    foreach ($actionsWaits as $tckt) {
        var_dump($tckt);
        if(null!=$client)
        {
            switch ($arrayType[$tckt['idPeticion']]) {
                case 'relay_off':
                    try {
                        $response = $client->__soapCall("RecibirDesconexiones", array(
                            $tckt
                        ));
                        $res = strtolower($response->RecibirDesconexionesResult->Respuesta);
                        if( $res == "ok" ||  $res == "OK"){
                            $update = $kaviAction->edit((string)$tckt['idPeticion'],array('status'=>'success'));
                        }
                    } catch (Exception $e) {
                        var_dump($e);
                    }
                break;
                case 'relay_on':
                    try {
                        $response = $client->__soapCall("RecibirReconexiones", array(
                            $tckt
                        ));
                        $res = strtolower($response->RecibirReconexionesResult->Respuesta);
                        if( $res == "ok" ||  $res == "OK" ){
                            $update = $kaviAction->edit((string)$tckt['idPeticion'],array('status'=>'success'));
                        }   
                    } catch (Exception $e) {
                        var_dump($e);
                    }
                    break;
            }
        }
    }
    print "</pre>";
    sleep(5);
}
?>
