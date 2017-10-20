<?php
namespace mx\gob\cfe\www;
use classes\Meter;
use classes\KaviAction;
class SoapApi
{

    private $IsAuthenticated;

    private $header;

    function __construct($hdr)
    {
        $this->IsAuthenticated = false;
        if ($hdr != null) {
            $this->header = simplexml_load_string($hdr);
        }
    }

    public function sentPeticion($params)
    {
        try {
            $options = array(
                'soap_version' => SOAP_1_2,
                'Usuario' => "PR073C54",
                'Clave' => "PR070"
            );
            $client = new \SoapClient("http://10.4.57.48/wsKaviCentinel/wsKaviCentinel.asmx?wsdl", $options);
            
            $ns = 'http://www.cfe.gob.mx/';
            $headerbody = array(
                'Usuario' => "PR073C54",
                'Clave' => "PR070"
            );
            $header = new \SOAPHeader($ns, 'AuthHeader', $headerbody);
            $client->__setSoapHeaders($header);
            $response = $client->__soapCall("RecibirLecturas", array(
                $params
            ));
        } catch (\Exception $e) {
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();
        }
    }

    public function LeerMedidores($idPeticion)
    {
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $listaPeticionesLectura = $idPeticion['listaPeticionesLectura'];
        $lecturaEnLinea = $idPeticion['lecturaEnLinea'];
        $limiteDiasAntiguedadLectura = $idPeticion['limiteDiasAntiguedadLectura'];
        $kaviAction = new KaviAction();
        $idPeticion = $idPeticion['idPeticion'];
        $response = array();
        foreach ($listaPeticionesLectura as $PeticionOperacionMedidor) {
            $requestMeter = \Functions::convertobjectToArray($PeticionOperacionMedidor);
            if (null == $requestMeter[0]) {
                $meter = new Meter();
                $meter->get($requestMeter['Medidor']['NumeroMedidor']);
                if($meter->idmeters<1){
                    continue;
                }
                foreach ($PeticionOperacionMedidor->ListaTiposLectura as $TiposLectura) {
                    $kaviAction->set(array(
                        'uuid_cfe' => $idPeticion,
                        'type_action' => $TiposLectura,
                        'meters' => array($meter->idmeters),
                    ));
                }
            } else {
                foreach ($requestMeter as $meter) {
                    
                    $objMeter = new Meter();
                    $objMeter->get($meter['Medidor']->NumeroMedidor);
                    if($objMeter->idmeters<1){
                        continue;
                    }
                    $meter['ListaTiposLectura'] = \Functions::convertobjectToArray($meter['ListaTiposLectura']);
                    if(true==is_array($meter['ListaTiposLectura']['TiposLectura'])){
                        foreach ($meter['ListaTiposLectura']['TiposLectura'] as $TiposLectura) {
                            $kaviAction->set(array(
                                'uuid_cfe' => $idPeticion,
                                'type_action' => $TiposLectura,
                                'meters' => array($objMeter->idmeters),
                            ));
                        }
                    }else{
                        $kaviAction->set(array(
                            'uuid_cfe' => $idPeticion,
                            'type_action' => $meter['ListaTiposLectura']['TiposLectura'],
                            'meters' => array($objMeter->idmeters),
                        ));
                    }
                }
            }
        }
        return array(
            "LeerMedidoresResult" => array(
                "Respuesta" => "OK",
                "CodigoError" => "Ninguno"
            )
        );
    }

    public function ComprobarEstadoServicios()
    {
        return array(
            'ComprobarEstadoServiciosResult' => array(
                'EstadoServicios' => 'Disponibles',
                'CodigoErrorPeticion' => 'Ninguno',
                'DescripcionProblemaEnServicios' => ''
            )
        );
    }
    
    public function DesconectarMedidores($idPeticion) {
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $listaMedidores = $idPeticion['listaMedidores'];
        $kaviAction = new KaviAction();
        $idPeticion = $idPeticion['idPeticion'];
        $response = array();
        foreach ($listaMedidores as $PeticionOperacionMedidor) {
            $requestMeter = \Functions::convertobjectToArray($PeticionOperacionMedidor);
            if (null == $requestMeter[0]) {
                $meter = new Meter();
                $meter->get($requestMeter['Medidor']['NumeroMedidor']);
                if($meter->idmeters<1){
                    continue;
                }
                $array = array();
                foreach ($requestMeter['ListaTiposLectura'] as $TiposLectura) {
                    $array[] = $TiposLectura;
                }
                $res = $kaviAction->set(array(
                    'uuid_cfe' => $idPeticion,
                    'type_action' => 'relay_off',
                    'meters' => array($meter->idmeters),
                    'parametres' => json_encode($array),
                ));
            } else {
                foreach ($requestMeter as $meter) {
                    $objMeter = new Meter();
                    $objMeter->get($meter['Medidor']->NumeroMedidor);
                    if($objMeter->idmeters<1){
                        continue;
                    }
                    $meter['ListaTiposLectura'] = \Functions::convertobjectToArray($meter['ListaTiposLectura']);
                    if(true==is_array($meter['ListaTiposLectura']['TiposLectura'])){
                        $array = array();
                        foreach ($meter['ListaTiposLectura']['TiposLectura'] as $TiposLectura) {
                            $array[] = $TiposLectura;
                        }
                        $kaviAction->set(array(
                            'uuid_cfe' => $idPeticion,
                            'type_action' => 'relay_off',
                            'meters' => array($objMeter->idmeters),
                            'parametres' => json_encode($array),
                        ));
                    }else{
                        $array = array();
                        $array[] = $meter['ListaTiposLectura']['TiposLectura'];
                        $kaviAction->set(array(
                            'uuid_cfe' => $idPeticion,
                            'type_action' => 'relay_off',
                            'meters' => array($objMeter->idmeters),
                            'parametres' => json_encode($array),
                        ));
                    }
                }
            }
        }
        $result =
        array(
            "DesconectarMedidoresResult" => array(
                "Respuesta" => "OK",
                "CodigoError" => "Ninguno"
            )
        );
        return $result;
    }
    public function ReconectarMedidores($idPeticion){
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $listaMedidores = $idPeticion['listaMedidores'];
        $kaviAction = new KaviAction();
        $idPeticion = $idPeticion['idPeticion'];
        $response = array();
        foreach ($listaMedidores as $PeticionOperacionMedidor) {
            $requestMeter = \Functions::convertobjectToArray($PeticionOperacionMedidor);
            if (null == $requestMeter[0]) {
                $meter = new Meter();
                $meter->get($requestMeter['Medidor']['NumeroMedidor']);
                if($meter->idmeters<1){
                    continue;
                }
                $array = array();
                foreach ($requestMeter['ListaTiposLectura'] as $TiposLectura) {
                    $array[] = $TiposLectura;
                }
                $res = $kaviAction->set(array(
                    'uuid_cfe' => $idPeticion,
                    'type_action' => 'relay_on',
                    'meters' => array($meter->idmeters),
                    'parametres' => json_encode($array),
                ));
            } else {
                foreach ($requestMeter as $meter) {
                    $objMeter = new Meter();
                    $objMeter->get($meter['Medidor']->NumeroMedidor);
                    if($objMeter->idmeters<1){
                        continue;
                    }
                    $meter['ListaTiposLectura'] = \Functions::convertobjectToArray($meter['ListaTiposLectura']);
                    if(true==is_array($meter['ListaTiposLectura']['TiposLectura'])){
                        $array = array();
                        foreach ($meter['ListaTiposLectura']['TiposLectura'] as $TiposLectura) {
                            $array[] = $TiposLectura;
                        }
                        $kaviAction->set(array(
                            'uuid_cfe' => $idPeticion,
                            'type_action' => 'relay_on',
                            'meters' => array($objMeter->idmeters),
                            'parametres' => json_encode($array),
                        ));
                    }else{
                        $array = array();
                        $array[] = $meter['ListaTiposLectura']['TiposLectura'];
                        $kaviAction->set(array(
                            'uuid_cfe' => $idPeticion,
                            'type_action' => 'relay_on',
                            'meters' => array($objMeter->idmeters),
                            'parametres' => json_encode($array),
                        ));
                    }
                }
            }
        }
        $result =
        array(
            "ReconectarMedidoresResult" => array(
                "Respuesta" => "OK",
                "CodigoError" => "Ninguno"
            )
        );
        return $result;
    }
    
    public function ActualizarDatosMedidores($idPeticion){
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $listaMedidores = $idPeticion['listaDatosMedidores'];
        $kaviAction = new KaviAction();
        $idPeticion = $idPeticion['idPeticion'];
        foreach ($listaMedidores as $medidor) {
            $requestMeter = \Functions::convertobjectToArray($medidor);
            if (null == $requestMeter[0]) {
                $meter = new Meter();
                $meter->get($requestMeter['Medidor']['NumeroMedidor']);
                
                if($meter->idmeters<1){
                    continue;
                }
                $arrayinfo = array(
                    'CodigoMedidor' => $requestMeter['CodigoMedidor'],
                    'LoteMedidor' => $requestMeter['CodigoMedidor'],
                    'EstadoMedidor' => $requestMeter['CodigoMedidor'],
                    'Cliente' => array(
                        'Rpu' => $requestMeter['Cliente']['Rpu'],
                        'Nombre' => $requestMeter['Cliente']['Nombre'],
                        'Direccion' => $requestMeter['Cliente']['Direccion'],
                        'Cuenta' => $requestMeter['Cliente']['Cuenta'],
                        'Tarifa' => $requestMeter['Cliente']['Tarifa'],
                        'NumeroHilos' => $requestMeter['Cliente']['NumeroHilos'],
                        'DemandaContratada' => $requestMeter['Cliente']['DemandaContratada'],
                        'EsBidireccional' => $requestMeter['Cliente']['EsBidireccional'],
                    )
                );
                $res = $kaviAction->set(array(
                    'uuid_cfe' => $idPeticion,
                    'type_action' => 'info',
                    'meters' => array(
                        $meter->idmeters
                    ),
                    'parametres' => json_encode($arrayinfo)
                ));
            } else {
                foreach ($requestMeter as $meter) {
                    $objMeter = new Meter();
                    $objMeter->get($meter['Medidor']->NumeroMedidor);
                    if($objMeter->idmeters<1){
                        continue;
                    }
                    $arrayinfo = array(
                        'CodigoMedidor' => $meter['CodigoMedidor'],
                        'LoteMedidor' => $meter['LoteMedidor'],
                        'EstadoMedidor' => $meter['EstadoMedidor'],
                        'Cliente' => array(
                            'Rpu' => $meter['Cliente']->Rpu,
                            'Nombre' => $meter['Cliente']->Nombre,
                            'Direccion' => $meter['Cliente']->Direccion,
                            'Cuenta' => $meter['Cliente']->Cuenta,
                            'Tarifa' => $meter['Cliente']->Tarifa,
                            'NumeroHilos' => $meter['Cliente']->NumeroHilos,
                            'DemandaContratada' => $meter['Cliente']->DemandaContratada,
                            'EsBidireccional' => $meter['Cliente']->EsBidireccional,
                        )
                    );
                    $kaviAction->set(array(
                        'uuid_cfe' => $idPeticion,
                        'type_action' => 'info',
                        'meters' => array($objMeter->idmeters),
                        'parametres' => json_encode($arrayinfo),
                    ));
                }
            }
        }
        $result = array(
            "ActualizarDatosMedidoresResult" => array(
                "Respuesta" => "OK",
                "CodigoError" => "Ninguno"
            )
        );
        return $result;
    }
    
    public function ConsultarDatosMedidores($idPeticion){
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $listaMedidores = $idPeticion['listaMedidores'];
        $kaviAction = new KaviAction();
        $idPeticion = $idPeticion['idPeticion'];
        foreach ($listaMedidores as $medidor) {
            $requestMeter = \Functions::convertobjectToArray($medidor);
            if (null == $requestMeter[0]) {
                $meter = new Meter();
                $meter->get($requestMeter['NumeroMedidor']);
                if($meter->idmeters==-1){
                    $meter->idmeters = -2;
                }
                $array = array(
                    'uuid_cfe' => $idPeticion,
                    'type_action' => 'info',
                    'meters' => array($meter->idmeters),
                ); 
                if(-2==$meter->idmeters){
                    $array['parametres']= json_encode(array($requestMeter['NumeroMedidor']));
                }
                $res = $kaviAction->set($array);
            } else {
                foreach ($requestMeter as $meter) {
                    $objMeter = new Meter();
                    $objMeter->get($meter['NumeroMedidor']);
                    if($meter->idmeters==-1){
                        $meter->idmeters = -2;
                    }
                    $array = array(
                        'uuid_cfe' => $idPeticion,
                        'type_action' => 'info',
                        'meters' => array($meter->idmeters),
                    );
                    if(-2==$meter->idmeters){
                        $array['parametres'] = json_encode(array($meter['NumeroMedidor']));
                    }
                    $kaviAction->set($array);
                }
            }
        }
        $result = array(
            "ConsultarDatosMedidoresResult" => array(
                "Respuesta" => "OK",
                "CodigoError" => "Ninguno"
            )
        );
        return $result;
    }
    
    public function ObtenerEventosMedidor($idPeticion){
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $serial_number = $idPeticion['medidorAConsultar']['Medidor']->NumeroMedidor;
        $init_date = '';
        $end_date = '';
        $arrayReading = array();
        foreach ($idPeticion['medidorAConsultar']['ListaTiposLectura'] as $TiposLectura) {
            $arrayReading['reading'][] = $TiposLectura;
        } 
        $arrayReading['start_date'] = $idPeticion['medidorAConsultar']['fechaDesde'];
        $arrayReading['end_date'] = $idPeticion['medidorAConsultar']['fechaHasta'];
        $idPeticion = $idPeticion['idPeticion'];
        $meter = new Meter();
        $meter->get($serial_number);
        $error = 'OK';
        if($meter->idmeters<1){
            $error = 'ERROR';
        }
        
        $kaviAction = new KaviAction();
        $array = array(
            'uuid_cfe' => $idPeticion,
            'type_action' => 'alerts',
            'meters' => array($meter->idmeters),
        );
        $array['parametres'] = json_encode($arrayReading);
        $kaviAction->set($array);
        $result = array(
            "ObtenerEventosMedidorResult" => array(
                "Respuesta" => $error,
                "CodigoError" => "Ninguno"
            )
        );
        return $result;
    }

    public function ConsultarEstadoPeticiones($idPeticion)
    {
        $idPeticion = \Functions::convertobjectToArray($idPeticion);
        $list_peticiones = $idPeticion['listaPeticiones'];
        $kavi = new KaviAction();
        $array = array();
        foreach ($list_peticiones as $value) {
            $kavi->get($value);
            $state = 'EnProceso';
            $kavi->status=='success' ? $state = 'Completada':$state = 'EnProceso';
            $PorcentajeAvance = 50;
            $kavi->status == 'success' ? $PorcentajeAvance = 100:$PorcentajeAvance = 50;
            $kavi->end_request = str_replace(" ", "T", $kavi->end_request); 
            if (- 1 != $kavi->idkavi_action) {
                $array['ConsultarEstadoPeticionesResult'][] = 
                 array(
                        'IdPeticion' => $kavi->uuid_cfe,
                        'EstadoPeticion' => $state,
                        'PorcentajeAvance' => $PorcentajeAvance,
                        'CodigoErrorPeticion' => 'Ninguno',
                        'DescripcionErrorPeticion' => '',
                        'FechaUltimoCambioEstado' => $kavi->end_request,
                    );
            }
        }
        return $array;
    }
}

?>
