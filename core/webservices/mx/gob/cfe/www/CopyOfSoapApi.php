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
        $limiteDiasAntiguedadLectura = [
            'limiteDiasAntiguedadLectura'
        ];
        $meter = new Meter();
        $kaviAction = new KaviAction();
        $idPeticion = $idPeticion['idPeticion'];
        $response = array();
        foreach ($listaPeticionesLectura as $PeticionOperacionMedidor) {
            $requestMeter = \Functions::convertobjectToArray($PeticionOperacionMedidor);
            if (NULL == $requestMeter[0]) {
                $meter->get($requestMeter['Medidor']['NumeroMedidor']);
                if($meter->idmeters<1){
                    continue;
                }
                foreach ($PeticionOperacionMedidor->ListaTiposLectura as $TiposLectura) {
                    $kaviAction->set(array(
                        'uuid_cfe' => $idPeticion,
                        'type_action' => $TiposLectura,
                        'meters' => array($meter->serial_number),
                    ));
                }
                
                $arrayReading = array();
                if (true == ! empty($serialNumber)) {
                    $date1 = date("Y-m-d H:i:s");
                    $date2 = date("Y-m-d H:i:s");
                    foreach ($PeticionOperacionMedidor->ListaTiposLectura as $TiposLectura) {
                        $query = "";
                        switch ($TiposLectura) {
                            case 'ConsumoDiurno':
                                break;
                            case 'ConsumoNocturno':
                                break;
                            case 'ConsumoEnergiaEntregada':
                                break;
                            case 'ConsumoEnergiaRecibida':
                                break;
                            case 'DemandaMaxima':
                                break;
                            default:
                                $statement2 = new \Cassandra\SimpleStatement("
                                INSERT INTO app_center.meter_by_tikectsKavi(
                                id_request,serial_number,type_request,param_request)
                                VALUES(?,?,?,?);");
                                $req2 = array(
                                    $idPeticion,
                                    $serialNumber,
                                    1,
                                    json_encode(array("reading"=>$TiposLectura))
                                );
                                $options2 = new \Cassandra\ExecutionOptions(array(
                                    'arguments' => $req2
                                ));
                                $session->execute($statement2, $options2);
                                
                                /* $query = "SELECT total_kwh_delivered,fake_timestamp from total_consumption_by_meter where serial_number='" . $serialNumber . "';";
                                $statement2 = new \Cassandra\SimpleStatement($query);
                                $future2 = $session->executeAsync($statement2);
                                $consumption = $future2->get();
                                foreach ($consumption as $total) {
                                    $totalDelivered = $total["total_kwh_delivered"];
                                    $date2 = $total["fake_timestamp"];
                                    if (- 1 != $totalDelivered) {
                                        $arrayReading[] = array(
                                            'lectura' => array(
                                                'tipo' => $TiposLectura,
                                                'valor' => $totalDelivered
                                            )
                                        );
                                    }
                                } */
                                break;
                        }
                    }
                    $date2 = date("Y-m-d H:i:s");
                    $response[] = array(
                        'ResultadoLecturasMedidor' => array(
                            'Medidor' => array(
                                'NumeroMedidor' => $serialNumber
                            ),
                            'ResultadoOperacion' => 'OK',
                            'CodigoErrorLectura' => 'ninguna',
                            'DescripcionErrorLectura' => '',
                            'FechaInicioLectura' => $date1,
                            'FechaFinLectura' => $date2,
                            'EstadoRelevador' => 'Abierto',
                            'EstadoMedidor' => 'Activo',
                            'ListaLecturas' => $arrayReading
                        )
                    );
                }
            } else {
                foreach ($requestMeter as $meter) {
                    $statement = new \Cassandra\SimpleStatement("SELECT serial_number FROM meters where serial_number='" . $meter['Medidor']->NumeroMedidor . "';");
                    $future = $session->executeAsync($statement);
                    $meters = $future->get();
                    $totalDelivered = - 1;
                    $serialNumber = '';
                    foreach ($meters as $row) {
                        $serialNumber = $row['serial_number'];
                    }
                    $arrayReading = array();
                    if (true == ! empty($serialNumber)) {
                        $date1 = date("Y-m-d H:i:s");
                        $date2 = date("Y-m-d H:i:s");
                        $meter['ListaTiposLectura'] = \Functions::convertobjectToArray($meter['ListaTiposLectura']);
                        
                        foreach ($meter['ListaTiposLectura'] as $TiposLectura) {
                            $query = "";
                            switch ($TiposLectura) {
                                case 'ConsumoDiurno':
                                    break;
                                case 'ConsumoNocturno':
                                    break;
                                case 'ConsumoEnergiaEntregada':
                                    break;
                                case 'ConsumoEnergiaRecibida':
                                    break;
                                case 'DemandaMaxima':
                                    break;
                                default:
                                    
                                    $statement2 = new \Cassandra\SimpleStatement("
                                    INSERT INTO app_center.meter_by_tikectsKavi(
                                    id_request,serial_number,type_request,param_request)
                                    VALUES(?,?,?,?);");
                                    $req2 = array(
                                        $idPeticion,
                                        $serialNumber,
                                        1,
                                        json_encode(array("reading"=>$TiposLectura))
                                    );
                                    $options2 = new \Cassandra\ExecutionOptions(array(
                                        'arguments' => $req2
                                    ));
                                    $session->execute($statement2, $options2);
                                    
                                    /* $query = "SELECT total_kwh_delivered,fake_timestamp from total_consumption_by_meter where serial_number='" . $serialNumber . "';";
                                    $statement2 = new \Cassandra\SimpleStatement($query);
                                    $future2 = $session->executeAsync($statement2);
                                    $consumption = $future2->get();
                                    foreach ($consumption as $total) {
                                        $totalDelivered = $total["total_kwh_delivered"];
                                        $date2 = $total["fake_timestamp"];
                                        if (- 1 != $totalDelivered) {
                                            $arrayReading[] = array(
                                                'lectura' => array(
                                                    'tipo' => $TiposLectura,
                                                    'valor' => $totalDelivered
                                                )
                                            );
                                        }
                                    } */
                                    break;
                            }
                        }
                        $date2 = date("Y-m-d H:i:s");
                        $response[] = array(
                            'ResultadoLecturasMedidor' => array(
                                'Medidor' => array(
                                    'NumeroMedidor' => $serialNumber
                                ),
                                'ResultadoOperacion' => 'OK',
                                'CodigoErrorLectura' => 'ninguno',
                                'DescripcionErrorLectura' => '',
                                'FechaInicioLectura' => $date1,
                                'FechaFinLectura' => $date2,
                                'EstadoRelevador' => 'Abierto',
                                'EstadoMedidor' => 'Activo',
                                'ListaLecturas' => $arrayReading
                            )
                        );
                    }
                }
            }
        }
        
        /* $params = array(
            "idPeticion" => $idPeticion,
            "listaResultados" => $response
        );
        self::sentPeticion($params); */
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
}

?>