<?php
namespace libs;

class testUnit
{
    /**
     * 
     * @param unknown $remote_ip
     * @param unknown $app
     * @param unknown $pass
     * @return mixed
     */
    public function testLogin($remote_ip,$app, $pass)
    {
        $verb = "/api/v1/login";
        $fields = array(
            'name_app' => urlencode($app),
            'secret_key' => urlencode($pass),
            'encrypted' => urlencode("false")
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    /**
     * 
     * @param unknown $remote_ip
     * @param unknown $token
     * @return mixed
     */
    public function testGetMeter($remote_ip,$token) {
        $verb = "/api/v1/meters";
        $headers = array(
            'Authorization: '.$token
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function testGetAlerts($remote_ip,$token,$dateStart,$dateEnd,$type,$limit=-1,$serie="") {
        $verb = "/api/v1/alarms?startDate=$dateStart&endDate=$dateEnd";
        if (-1!=$limit) {
            $verb.="&limit=$limit";
        }
        if (true==!empty($type)) {
            $verb.="&type=$type";
        }
        if (true==!empty($serie)) {
            $verb.="&num_serie=$serie";
        }
        $headers = array(
            'Authorization: '.$token
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function testAddMeter($remote_ip,$token,$meter) {
        $verb = "/api/v1/meters";
        $fields = array(
            'DATA' => urlencode($meter),
        );
        $headers = array(
            'Authorization: '.$token
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function deleteMeter($remote_ip,$token,$serial) {
        $verb = "/api/v1/meters/$serial";       
        $headers = array(
            'Authorization: '.$token
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function testSetRelay($remote_ip,$token,$meter) {
        $verb = "/api/v1/meters/state_relay";
        $fields = array(
            'DATA' => urlencode($meter),
        );
        $headers = array(
            'Authorization: '.$token
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function testResetMeter($remote_ip,$token,$meter) {
        $verb = "/api/v1/meters/reset_meter";
        $fields = array(
            'DATA' => urlencode($meter),
        );
        $headers = array(
            'Authorization: '.$token
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_ip . $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

?>