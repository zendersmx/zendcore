<?php
/**
 * @class: Database (PHP5-Strict with comments)
 * @project: PHP class of AICore framework
 * @date: 2015-07-12
 * @version: 1.0.0_php5
 * @author: Zenders Technology S.A de C.V
 * @copyright: Copyright (c) 2017-2020 Zenders Technology S.A de C.V
 * @email: support <at> zenders <dot> mx
 * @descrip: 
 *              Copyright � 2017-2020
 *              Zenders Technology S.A de C.V
 *              All Rights Reserved.
 *  The contents of this file, and the files included with this file, 
 *  are subject to the current version of Common Development and Distribution License, 
 *  Version 1.0 only (the License), which can be obtained at http://zenders.mx/licence. 
 *  You may not use this file except in compliance with the License.
 *  The original code, and all software distributed under the License, are distributed and made 
 *  available on an "AS IS" basis, WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED. 
 *  Adrian Maldonado Cano HEREBY DISCLAIMS ALL SUCH WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR PARTICULAR PURPOSE, OR NON INFRINGEMENT. Please see the License
 *  for the specific language governing rights and limitations under the License.
 *  When distributing Covered Software, include this CDDL Header in each file and include the License file 
 *  at http://zendersolutions.com.mx/licence.  If applicable, add the following below this 
 *  CDDL HEADER, with the fields enclosed by brackets [   ] replaced with your own identifying information: 
 *  Portions Copyright [yyyy]  [name of copyright owner] 
 */
namespace libs;

abstract class Database
{

    /**
     *
     * @var SQLconnexion $connexion
     */
    private $connexion = NULL;

    private $isConnected = false;

    /**
     *
     * @var Integer $numConexion
     */
    private $numqQueries = 0;

    /**
     *
     * @var String $host
     */
    private $host;

    /**
     *
     * @var String $database
     */
    private $database;

    /**
     *
     * @var String $user
     */
    private $user;

    /**
     *
     * @var String $password
     */
    private $password;

    /**
     *
     * @var Integer $port
     */
    private $port = 3306;

    /**
     *
     * @var String $type_driver
     */
    private $type_driver = 'mysqli';

    /**
     *
     * @var Integer $thread_id
     */
    private $thread_id;

    private $flag=NULL;
    
    
    /**
     * @param field_type $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

	/**
     */
    function __construct()
    {
        $arg = func_get_args();
        $numArg = func_num_args();
        for ($i = 0; $i < $numArg; $i ++) {
            switch ($i) {
                case 0:
                    $this->host = $arg[$i];
                    break;
                case 1:
                    $this->database = $arg[$i];
                    break;
                case 2:
                    $this->user = $arg[$i];
                    break;
                case 3:
                    $this->password = $arg[$i];
                    break;
                case 4:
                    is_numeric($arg[$i]) ? $this->port = $arg[$i] : $this->port = 13306;
                    break;
                case 5:
                    switch ($arg[$i]) {
                        case 'mysqli':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'sqlite':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'mysql':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'postgresql':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'oracle':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'cassandra':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'mongodb':
                            $this->type_driver = $arg[$i];
                            break;
                        case 'mariadb':
                            $this->type_driver = $arg[$i];
                            break;
                        default:
                            $this->type_driver = 'mysqli';
                            break;
                    }
                    break;
            }
        }
        if (true == empty($this->host)) {
            true == defined('HOST_DATABSE') ? $this->host = HOST_DATABSE : $this->host = '';
        }
        
        if (true == empty($this->database)) {
            true == defined('SCHEMA_DATABASE') ? $this->database = SCHEMA_DATABASE : $this->database = '';
        }
        
        if (true == empty($this->user)) {
            true == defined('USER_DATABASE') ? $this->user = USER_DATABASE : $this->user = '';
        }
        if (true == empty($this->password)) {
            true == defined('PASSWORD_DATABASE') ? $this->password = PASSWORD_DATABASE : $this->password = '';
        }
        $this->type_driver = strtolower(TYPE_DATABASE);
        if (true == defined('PORT_DATABASE')) {
            $this->port = PORT_DATABASE;
        } else {
            $this->type_driver == 'mysqli' ? $this->port = 3306 : $this->port = 5432;
        }
        self::createConnexion($this->type_driver);
    }

    /**
     */
    public function __destruct()
    {
        self::closeConnexion();
        unset($this->connexion);
        unset($this);
    }

    public function __toString()
    {
        return "<br><b>Current Settings</b>: <br><b>HOST</b>:$this->host<br><b>SCHEMA</b>:$this->database<br><b>USER</b>: $this->user<br><b>PASSWORD</b>: $this->password<br><b>PORT</b>: $this->port<br><b>DRIVER</b>: $this->type_driver<br>";
    }

    
    
    /**
     * This method will allow to create an abstract object class
     */
    abstract function get($key);
    
    /**
     * This method will allow to create an abstract object class
     */
    abstract function set($data = array());
    
    /**
     * This abstract method allow you to edit an object class
     */
    abstract function edit($key,$data=array());
    
    /**
     * This abstract method to eliminate an object of the class
     */
    abstract function delete($key);
    
    /**
     * This abstract method allows you to search an object class
     */
    abstract function exists($key);
    
    /**
     * This abstract method allows you to list object of class
     */
    abstract function lists($start=-1,$end=-1);
    
    /**
     * 
     */
    private function createConnexion()
    {
        if (true == isset($GLOBALS['CONEXION'])) {
            if(null != $GLOBALS['CONEXION']){
                $this->connexion = &$GLOBALS['CONEXION'];
                switch ($this->type_driver) {
                    case 'mysqli':
                        if(false == mysqli_ping($this->connexion)){
                            self::connect();
                        }
                        break;
                    case 'sqlite':
                        
                        break;
                    case 'postgresql':
                        if(false == pg_ping($this->connexion)){
                            self::connect();
                        }
                        break;
                }
                $this->isConnected = true;
            }
        }
        if(false == $this->isConnected ){
            switch ($this->type_driver) {
                case 'mysqli':
                    $GLOBALS['CONEXION'] = @new \mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
                    $this->connexion =  &$GLOBALS['CONEXION'];
                    if ($this->connexion->connect_error) {
                        exit(1);
                    } 
                    else{
                        $this->thread_id = $this->connexion->thread_id;
                        $this->isConnected = true;
                    }
                    break;
                case 'sqlite':
                    if (NULL == $this->flag) {
                        $GLOBALS['CONEXION'] = new \SQLite3($this->database, SQLITE3_OPEN_READWRITE);
                        $this->connexion = $GLOBALS['CONEXION'];
                    }else{
                        $GLOBALS['CONEXION'] = new \SQLite3($this->database, $this->flag);
                        $this->connexion = $GLOBALS['CONEXION'];
                    }
                    $this->connexion->busyTimeout(10000);
                    $this->isConnected = true;
                    break;
                case 'mysql':
            
                    break;
                case 'postgresql':
                    $string_con = "host=$this->host port=$this->port dbname=$this->database user=$this->user password=$this->password connect_timeout=5";
                    $GLOBALS['CONEXION'] = \pg_connect($string_con);
                    $this->connexion =  &$GLOBALS['CONEXION'];
                    if($this->connexion){
                        $this->isConnected = true;
                    }
                    break;
                case 'oracle':
            
                    break;
                case 'cassandra':
            
                    break;
                case 'mongodb':
            
                    break;
                case 'mariadb':
            
                    break;
            }   
        }
    }
    
    /**
     * conect resource with the database
     * @return boolean
     */
    public function connect(){
        $result = false;
        if (true == isset($GLOBALS['CONEXION']) & null != $GLOBALS['CONEXION']) {
            $this->isConnected = true;
            $this->conexion = &$GLOBALS['CONEXION'];
        }
        if (false === $this->isConnected) {
        switch ($this->type_driver) {
                case 'mysqli':
                    $GLOBALS['CONEXION'] = new \mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
                    $this->connexion = &$GLOBALS['CONEXION'];
                    if ($this->connexion->connect_error) {
                        exit(1);
                    } else
                    {
                        $this->thread_id = $this->connexion->thread_id;
                        $this->isConnected = true;
                    }
                    break;
                case 'sqlite':
                    if (NULL == $this->flag) {
                        $this->connexion = new \SQLite3($this->database, SQLITE3_OPEN_READWRITE);
                    }else{
                        $this->connexion = new \SQLite3($this->database, $this->flag);
                    }
                    $this->connexion->busyTimeout(10000);
                    $this->isConnected = true;
                    break;
                case 'mysql':
            
                    break;
                case 'postgresql':
                    $string_con = "host=$this->host port=$this->port dbname=$this->database user=$this->user password=$this->password connect_timeout=5";
                    $GLOBALS['CONEXION'] = \pg_connect($string_con);
                    $this->connexion =  &$GLOBALS['CONEXION'];
                    if($this->connexion){
                        $this->isConnected = true;
                    }
                    break;
                case 'oracle':
            
                    break;
                case 'cassandra':
            
                    break;
                case 'mongodb':
            
                    break;
                case 'mariadb':
            
                    break;
            }    
        }
        return $result;
    }

    /**
     *
     * @return boolean
     */
    public function closeConnexion()
    {
        $result = false;
        if (true === $this->isConnected) {
            switch ($this->type_driver) {
                case 'mysqli':
                    if(true == property_exists(get_called_class(), "connexion")){
                        if(NULL != $this->connexion ){
                            if (true == mysqli_ping($this->connexion)) {
                                $result = $this->connexion->close();
                            }    
                        }    
                    }
                    break;
                case 'sqlite':
                    $result = $this->connexion->close();
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                    if (NULL != $this->connexion) {
                        if(get_resource_type($this->connexion)!="Unknown"){
                            if (true == pg_ping($this->connexion)) {
                                pg_close($this->connexion);
                            }    
                        }
                    }
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }
        }
        return $result;
    }
    
    public function openConnexion($flag=NULL)
    {
        $result = false;
        if (true === $this->isConnected) {
            switch ($this->type_driver) {
                case 'mysqli':
                    $result = $this->connexion->open();
                    break;
                case 'sqlite':
                    if(NULL!=$flag){
                        $result = $this->connexion->open($this->database,$flag);
                    }else{
                        $result = $this->connexion->open($this->database);
                    }
                    
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                    $string_con = "host=$this->host port=$this->port dbname=$this->database user=$this->user password=$this->password connect_timeout=5";
                    $GLOBALS['CONEXION'] = \pg_connect($string_con);
                    $this->connexion =  &$GLOBALS['CONEXION'];
                    if($this->connexion){
                        $result = true;
                    }
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }
        }
        return $result;
    }

    /**
     *
     * @param string $sql            
     * @return mysqli_result $result
     */
    protected function query($sql)
    {
        $result = NULL;
        if (true == empty($sql)) {
            return $result;
        }
        if (true === $this->isConnected) {
            switch ($this->type_driver) {
                case 'mysqli':
                        $this->connexion->query("SET NAMES UTF8");
                        $result = $this->connexion->query($sql);
                    break;
                case 'sqlite':
                    $result = $this->connexion->query($sql);
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                        $sql = str_replace("\"", "'", $sql);
                        $result = \pg_query($this->connexion, $sql);
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }
            ++ $this->numqQueries;
        }
        return $result;
    }
    
    /**
     * 
     * @param multiple $result
     * @return array $dataArray
     */
    protected function associateResult($result)
    {
        $dataArray = array();
        switch ($this->type_driver) {
            case 'mysqli':
                if (true == $result instanceof \mysqli_result) {
                    if ($result->num_rows > 0) {
                        $dataArray = $result->fetch_assoc();
                    }
                }
                break;
                case 'sqlite':
                    if ($result instanceof  \SQLite3Result ){
                        while($res = $result->fetchArray(SQLITE3_ASSOC)) {
                            $dataArray[] = $res;
                        }   
                    }
                    break;
            case 'mysql':
                
                break;
            case 'postgresql':
                $dataArray = pg_fetch_assoc($result);
                break;
            case 'oracle':
                
                break;
            case 'cassandra':
                
                break;
            case 'mongodb':
                
                break;
            case 'mariadb':
                
                break;
        }
        return $dataArray;
    }
    
    protected function queryAssc($sql)
    {
        $result = array();
        if (true == empty($sql)) {
            return $result;
        }
        if (true === $this->isConnected) {
            switch ($this->type_driver) {
                case 'mysqli':
                    $this->connexion->query("SET NAMES UTF8");
                    $result_query = $this->connexion->query($sql);
                    if ($result_query instanceof \mysqli_result) {
                        if ($result_query->num_rows > 0) {
                            foreach ($row = $result_query->fetch_assoc() as $key => $value) {
                                $result[$key] = $value;
                            }
                        }
                    }
                    break;
                case 'sqlite':
                    $result_query = $this->connexion->query($sql);
                    if ($result_query instanceof \SQLite3Result) {
                        if($result_query->columnType(0) != SQLITE3_NULL){
                            foreach (($result = $result_query->fetchArray(SQLITE3_ASSOC)) as $key => $value) {
                                $result[$key] = $value;
                            }    
                        }
                    }
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }
            ++ $this->numqQueries;
        }
        
        return $result;
    }
    
    protected function queryAssc_v2($sql)
    {
        $result = array();
        if (true == empty($sql)) {
            return $result;
        }
        if (true === $this->isConnected) {
            switch ($this->type_driver) {
                case 'mysqli':
                    $this->connexion->query("SET NAMES UTF8");
                    $result_query = $this->connexion->query($sql);
                    if ($result_query instanceof \mysqli_result) {
                        if ($result_query->num_rows > 0) {
                            for ($i = 0; $i < $result_query->num_rows; $i ++) {
                                $row = $result_query->fetch_assoc();
                                $result[] = $row;
                            }
                        }
                    }
                    break;
                case 'sqlite':
                    $result_query = $this->connexion->query($sql);
                    if ($result_query instanceof \SQLite3Result) {
                        while ($res = $result_query->fetchArray(SQLITE3_ASSOC)) {
                            $result[] = $res;
                        }
                    }
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }
            ++ $this->numqQueries;
        }
        return $result;
    }

    /**
     *
     * @param String $sql            
     * @param multiple parametres
     * @return boolean 
     * @example 
     * $query = "SELECT * FROM example WHERE uid='?' AND other_champ='?'";<br>
     * $param = 'xxxx';<br>
     * $param2 = 'xxxxx';<br>
     * $result = <b>parent::existsBySql</b>($query,$param , $param2);<br>
     *          if(true == $result){<br>
     *           ... <br>
     *          }<br>
     */
    protected function existsBySql($sql)
    {
        $result = false;
        $arg = func_get_args();
        $numArg = func_num_args();
        $start = 0;
        $toFind = "?";
        $newstr = "";
        $i = 1;
        while ($pos = strpos($sql, $toFind, $start)) {
            if (strlen($newstr) > 0) {
                $newstr = substr($newstr, 0, $pos);
            }
            for ($x = 0; $x < $pos; $x ++) {
                if (strlen($newstr) > 0) {
                    $newstr[$x] = $sql[$x];
                } else {
                    $newstr .= $sql[$x];
                }
            }
            if (true == isset($arg[$i])) {
                $newstr .= addslashes(htmlspecialchars(htmlentities(trim($arg[$i]))));
            }else{
                $newstr .= " ";
            }
            $start = $pos + 1;
            for ($f = $start; $f < strlen($sql); $f ++) {
                $newstr .= $sql[$f];
            }
            $sql = $newstr;
            $i ++;
        }
        $pos = strpos($sql, "limit", $start);
        if (false != $pos) {
            $sql = substr($sql, 0, $pos);
        }
        if ($this->type_driver=="postgresql") {
            $sql .= " limit 1 offset 0";
        }else{
            $sql .= " limit 0,1";
        }
        $result_query = $this->query($sql);
        if (true === $this->isConnected) {
            switch ($this->type_driver) {
                case 'mysqli':
                    if ($result_query instanceof \mysqli_result || true == $result_query) {
                        if ($result_query->num_rows > 0) {
                            $result = true;
                        }
                    }
                    break;
                case 'sqlite':
                    $result_query = $this->connexion->query($sql);
                    if ($result_query instanceof \SQLite3Result) {
                        $it = 0;
                        while ($res = $result_query->fetchArray(SQLITE3_ASSOC)) {
                            $it ++;
                        }
                        if ($it > 0) {
                            $result = true;
                        }
                    }
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                    if($result_query){
                        if (pg_num_rows($result_query) > 0) {
                            $result = true;
                        }
                    }
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }    
        }
        return $result;
    }
    
    protected function countRowsOnSql($sql){
        $result = 0;
        if (true === $this->isConnected) {
            $result_query = $this->query($sql);
            switch ($this->type_driver) {
                case 'mysqli':
                    if (true === $this->isConnected) {
                        if ($result_query instanceof \mysqli_result) {
                            if ($result_query->num_rows > 0) {
                                $result = $result_query->num_rows;
                            }
                        }
                    }
                    break;
                case 'sqlite':
                    $result_query = $this->connexion->query($sql);
                    if ($result_query instanceof \SQLite3Result) {
                        $it = 0;
                        while ($res = $result_query->fetchArray(SQLITE3_ASSOC)) {
                            $it ++;
                        }
                        $result = $it; 
                    }
                    break;
                case 'mysql':
                    break;
                case 'postgresql':
                    break;
                case 'oracle':
                    break;
                case 'cassandra':
                    break;
                case 'mongodb':
                    break;
                case 'mariadb':
                    break;
            }    
        }
        
        return $result;
    }

    /**
     *
     * @param String $sql            
     * @param multiple parametres
     * @return array <multitype:, unknown>
     * @example 
     * $query = "SELECT * FROM example WHERE uid='?' AND other_champ='?'";<br>
     * $param = 'xxxx';<br>
     * $param2 = 'xxxxx';<br>
     * $result = <b>parent::findOneBySql</b>($query,$param , $param2);<br>
     *          var_dump($result);
     */
    protected function findOneBySql($sql)
    {
        $result = array();
        $arg = func_get_args();
        $numArg = func_num_args();
        $start = 0;
        $toFind = "?";
        $newstr = "";
        $i = 1;
        while ($pos = strpos($sql, $toFind, $start)) {
            if (strlen($newstr) > 0) {
                $newstr = substr($newstr, 0, $pos);
            }
            for ($x = 0; $x < $pos; $x ++) {
                if (strlen($newstr) > 0) {
                    $newstr[$x] = $sql[$x];
                } else {
                    $newstr .= $sql[$x];
                }
            }
            if (true == isset($arg[$i])) {
                $newstr .= addslashes(htmlspecialchars(htmlentities(trim($arg[$i]))));
            }else{
                $newstr .= " ";
            }
            $start = $pos + 1;
            for ($f = $start; $f < strlen($sql); $f ++) {
                $newstr .= $sql[$f];
            }
            $sql = $newstr;
            $i ++;
        }
        $pos = strpos($sql, "limit", $start);
        if (false != $pos) {
            $sql = substr($sql, 0, $pos);
        }
    if ($this->type_driver == "postgresql") {
            $sql .= " limit 1 offset 0";
        } else {
            $sql .= " limit 0,1";
        }
        $result_query = $this->query($sql);
        switch ($this->type_driver) {
            case 'mysqli':
                if ($result_query instanceof \mysqli_result) {
                    if ($result_query->num_rows > 0) {
                        foreach ($row = $result_query->fetch_assoc() as $key => $value) {
                            $result[$key] = $value;
                        }
                    }
                }
                break;
            case 'sqlite':
                if ($result_query instanceof \SQLite3Result) {
                    $result = $result_query->fetchArray(SQLITE3_ASSOC);
                }
                break;
            case 'mysql':
                
                break;
            case 'postgresql':
                
                break;
            case 'oracle':
                
                break;
            case 'cassandra':
                
                break;
            case 'mongodb':
                
                break;
            case 'mariadb':
                
                break;
        }
        return $result;
    }
    
    protected function findOneId($sql) {
        
    }

    /**
     *
     * @param string $table
     *            Name of table to get rows
     * @param Array $fields            
     * @param string $clause            
     * @param number $debug            
     * @param string $orderby            
     * @param string $typeOrder            
     * @param number $start            
     * @param number $end            
     * @return multitype:Array
     * @example 
     * $result = $database->selectRows("api",array('account_sid','name_app','',0,'account_sid','desc',0,10));<br>
     * var_dump($result);
     */
    protected function selectRows($table, $field = array(), $clause = '', $debug = false, $orderby = '', $typeOrder = '', $start = 0, $end = 0)
    {
        $result = array();
        $stringQuery = "Select ";
        $sqlColumnas = "";
        $sqlClausulas = "";
        if (true == ! empty($field)) {
            for ($iterador = 0; $iterador < count($field); ++ $iterador) {
                if ($iterador == count($field) - 1) {
                    $sqlColumnas .= "" . $field[$iterador] . "";
                } else {
                    $sqlColumnas .= "" . $field[$iterador] . ", ";
                }
            }
        } else
            $sqlColumnas .= "*";
        $stringQuery .= $sqlColumnas;
        $stringQuery .= " from " . $table . " ";
        if ($clause !== '')
            $stringQuery .= "where " . $clause;
        if ($orderby !== '' && $typeOrder !== "")
            $stringQuery .= " ORDER BY " . $orderby . " " . $typeOrder;
        if ($end > $start){
            $end=$end-$start;
            $stringQuery .= " LIMIT " . $start . "," . $end;
        }
        $stringQuery .= " ; ";
        
        if (true ==$debug || 1 == $debug)
            echo ($stringQuery);
        $result_query = $this->query($stringQuery);
        switch ($this->type_driver) {
            case 'mysqli':
                if ($result_query instanceof \mysqli_result) {
                    if ($result_query->num_rows > 0) {
                        for ($i = 0; $i < $result_query->num_rows; $i++) {
                            $row = $result_query->fetch_assoc();
                            $tmp = array();
                            foreach ($row as $key => $value) {
                                $tmp[$key] = $value;
                            }
                            $result[] = $tmp;
                        }
                    }
                }
                break;
            case 'sqlite':
                if ($result_query instanceof  \SQLite3Result ){
                    while($res = $result_query->fetchArray(SQLITE3_ASSOC)) {
                        $result[]=$res;
                    }
                }
               break;
            case 'mysql':
                
                break;
            case 'postgresql':
                   if($result_query){
                       while ($row = \pg_fetch_assoc($result_query)) {
                           $result[]=$row;
                       }
                   }
                break;
            case 'oracle':
                
                break;
            case 'cassandra':
                
                break;
            case 'mongodb':
                
                break;
            case 'mariadb':
                
                break;
        }
        return $result;
    }
    
   /**
    * 
    * @param string $table
    * @param array $fields
    * @param string $clause [ optional ]
    * @param number $debug [ optional ]
    * @return number
    * @example
    * $table = 'example'; <br>
    * $data = array('idexample'=>3,'nameExample'=>'test'); <br>
    * $newID = <b>parent::insertRow</b>($table,$data); <br>
    * var_dump($newID);
    */
    protected function insertRow($table, $fields = array(), $debug = false)
    {
        $result = -1;
        $sql = "INSERT INTO $table";
        if (true == is_array($fields)) {
            $size = count($fields);
            $i = 0;
            $col = " (";
            $values = " VALUES(";
            foreach ($fields as $key => $value) {
                if ($i == ($size - 1)) {
                    $col .= trim($key);
                    $values .= "'" . trim($value) . "'";
                } else {
                    $col .= trim($key). ",";
                    $values .= "'" . trim($value )."',";
                }
                $i ++;
            }
            $values .= ")";
            $col .= ")";
            $sql .= $col . $values . ";";
            if (true ==$debug || 1==$debug)
                echo "<pre>$sql</pre>";
        }
        if (true == is_array($fields)) {
            switch ($this->type_driver) {
                case 'mysqli':
                    $resultQuery = $this->query($sql);
                    if ($resultQuery instanceof \mysqli_result || true == $resultQuery) {
                        $result = $this->connexion->insert_id;
                    }
                    break;
                case 'sqlite':
                    $resultQuery = $this->connexion->exec($sql);
                    if (true == $resultQuery) {
                        $result = $this->connexion->lastInsertRowID();
                    }
                    break;
                case 'mysql':
            
                    break;
                case 'postgresql':
                    $resultQuery = $this->query($sql);
                    $insert_id = pg_last_oid($resultQuery);
                    if ($insert_id!==false) {
                        $command_id =\pg_query($this->connexion, 'SELECT lastval();');
                        if ($command_id!==false) {
                            $result = \pg_fetch_row($command_id);
                            $result  = $result[0];
                        }
                    }
                    break;
                case 'oracle':
            
                    break;
                case 'cassandra':
            
                    break;
                case 'mongodb':
            
                    break;
                case 'mariadb':
            
                    break;
            }   
        }
        return $result;
    }
    
    /**
     * 
     * @param string $table
     * @param array $fields
     * @param string $clause [ optional ]
     * @param number $debug [ optional ]
     * @return number
     * @example
     * $table = 'example'; <br>
     * $clause = ' id_meter ="'.$key.'"'; <br>
     * $data = array('idexample'=>4,'nameExample'=>'test'); <br>
     * $numRowEdit = <b>parent::updateRow</b>($table,$data,$clause); <br>
     * var_dump($numRowEdit);
     */
    protected function updateRow($table, $fields = array(), $clause = '', $debug = false)
    {
        $result = -1;
        $sql = "update $table set ";
        if (true == is_array($fields)) {
            $size = count($fields);
            $i = 0;
            foreach ($fields as $key => $value) {
                if ($i == ($size - 1)) {
                    $sql .= addslashes(htmlspecialchars(htmlentities(trim($key)))) . "='" . addslashes(htmlspecialchars(htmlentities(trim($value)))) . "' ";
                } else {
                    $sql .= addslashes(htmlspecialchars(htmlentities(trim($key)))) . "='" . addslashes(htmlspecialchars(htmlentities(trim($value)))) . "', ";
                }
                $i ++;
            }
            if ($clause != "") {
                $sql .= "WHERE " . $clause . ";";
            } else
                $sql .= ";";
            if ($debug == true || $debug == 1)
                var_dump($sql);
        
        }
        if(true==$this->isConnected){
            if (true == is_array($fields)) {
                switch ($this->type_driver) {
                    case 'mysqli':
                        $resultQuery = $this->query($sql);
                        if ($resultQuery instanceof \mysqli_result || true == $resultQuery) {
                            $result = $this->connexion->affected_rows;
                        }
                        break;
                    case 'sqlite':
                        $resultQuery = $this->connexion->exec($sql);
                        if (true == $resultQuery) {
                            $result = $this->connexion->changes();
                        }
                        break;
                    case 'mysql':
            
                        break;
                    case 'postgresql':
            
                        break;
                    case 'oracle':
            
                        break;
                    case 'cassandra':
            
                        break;
                    case 'mongodb':
            
                        break;
                    case 'mariadb':
            
                        break;
                }
            }    
        }
        return $result;
    }
    
    /**
     * 
     * @param string $table
     * @param string $clause [ optional ]
     * @param number $debug [ optional ]
     * @return number
     * @example
     * $table = "example"; <br>
     * $clause = ' id_meter ="'.$key.'"'; <br>
     * $numRowsDelete = <b>parent::dropRow</b>($table,$clause);<br>
     * var_dump($numRowsDelete);
     */
    protected function dropRow($table, $clause, $debug = false)
    {
        $result = -1;
        $sql = 'DELETE FROM ';
        $sql .= $table;
        $result = - 1;
        if (! empty($clause) || $clause !== '') {
            $sql .= ' where ' . $clause . ';';
        }
        if (true == $debug || 1 == $debug)
            echo "<pre>$sql</pre>";
        switch ($this->type_driver) {
            case 'mysqli':
                $resultQuery = $this->query($sql);
                if ($resultQuery instanceof \mysqli_result || true == $resultQuery) {
                    $result = $this->connexion->affected_rows;
                }
                break;
            case 'sqlite':
                $resultQuery = $this->connexion->exec($sql);
                if (true == $resultQuery) {
                    $result = $this->connexion->changes();
                }
                break;
            case 'mysql':
                break;
            case 'postgresql':
                $resultQuery = $this->query($sql);
                if ($resultQuery) {
                    $result = pg_affected_rows($resultQuery);
                }
                break;
            case 'oracle':
        
                break;
            case 'cassandra':
        
                break;
            case 'mongodb':
        
                break;
            case 'mariadb':
        
                break;
        }
        return $result;
    }
    

    /**
     *
     * @return integer $numqQueries
     */
    public function getTotalQueries()
    {
        return $this->numqQueries;
    }

    /**
     * 
     * @return boolean
     */
    protected function initTransaccion()
    {
        $result = false;
        switch ($this->type_driver) {
            case 'mysqli':
                $result = $this->connexion->autocommit(false);
                break;
            case 'mysql':
                
                break;
            case 'postgresql':
                
                break;
            case 'oracle':
                
                break;
            case 'cassandra':
                
                break;
            case 'mongodb':
                
                break;
            case 'mariadb':
                
                break;
        }
        return $result;
    }
    
    /**
     * 
     * @return boolean
     */
    protected function rollback() {
        $result = false;
        switch ($this->type_driver) {
            case 'mysqli':
                $result = $this->connexion->rollback();
                break;
            case 'mysql':
        
                break;
            case 'postgresql':
        
                break;
            case 'oracle':
        
                break;
            case 'cassandra':
        
                break;
            case 'mongodb':
        
                break;
            case 'mariadb':
        
                break;
        }
        return $result;
    }
    
    /**
     * 
     * @return boolean
     */
    protected function commit() {
        $result = false;
        switch ($this->type_driver) {
            case 'mysqli':
                $result = $this->connexion->commit();
                break;
            case 'mysql':
        
                break;
            case 'postgresql':
        
                break;
            case 'oracle':
        
                break;
            case 'cassandra':
        
                break;
            case 'mongodb':
        
                break;
            case 'mariadb':
        
                break;
        }
        return $result;
    }

    /**
     * 
     * @param unknown $user
     * @param unknown $pass
     * @param unknown $dabaseName
     * @param string $type_driver
     * @param string $host
     * @return boolean
     * @example
     * $result = Database::existDatabase(USER_DATABASE, PASSWORD_DATABASE, SCHEMA_DATABASE);<br>
     * var_dump($result);
     */
    public static function existDatabase($user, $pass, $dabaseName,$type_driver='mysqli', $host = 'localhost')
    {
        $result = false;
        $type_driver = strtolower($type_driver);
        switch ($type_driver) {
            case 'mysqli':
                $con = new \mysqli($host, $user, $pass);
                $result = $con->select_db($dabaseName);
                break;
            case 'mysql':
                
                break;
            case 'postgresql':
                
                break;
            case 'oracle':
                
                break;
            case 'cassandra':
                
                break;
            case 'mongodb':
                
                break;
            case 'mariadb':
                
                break;
        }
        return $result;
    }

    
    /**
     * 
     * @param unknown $user
     * @param unknown $pass
     * @param unknown $dabaseName
     * @param string $type_driver
     * @param string $host
     * @return boolean
     * @example 
     * $result = Database::createDatabase(USER_DATABASE, PASSWORD_DATABASE,SCHEMA_DATABASE);<br>
     * var_dump($result);
     */
    public static function createDatabase($user, $pass, $dabaseName, $type_driver = 'mysqli', $host = 'localhost')
    {
        $result = false;
        $type_driver = strtolower( $type_driver );
        switch ($type_driver) {
            case 'mysqli':
                $con = new \mysqli($host, $user, $pass);
                $sql = "CREATE DATABASE IF NOT EXISTS " . $dabaseName . ";";
                $result = $con->query($sql);
                break;
            case 'mysql':
                
                break;
            case 'postgresql':
                
                break;
            case 'oracle':
                
                break;
            case 'cassandra':
                
                break;
            case 'mongodb':
                
                break;
            case 'mariadb':
                
                break;
        }
        return $result;
    }
    
    /**
     * 
     * @param unknown $user
     * @param unknown $pass
     * @param unknown $dabaseName
     * @param string $type_driver
     * @param string $host
     * @return boolean $result
     * @example
     * $result=Database::dropDatabase(USER_DATABASE, PASSWORD_DATABASE, 'new');<br>
     * var_dump($result);
     */
    public static function dropDatabase($user, $pass, $dabaseName, $type_driver = 'mysqli', $host = 'localhost')
    {
        $result = false;
        $type_driver = strtolower($type_driver);
        switch ($type_driver) {
            case 'mysqli':
                $con = new \mysqli($host, $user, $pass);
                $sql = "DROP DATABASE " . $dabaseName . ";";
                $result = $con->query($sql);
                break;
            case 'mysql':
        
                break;
            case 'postgresql':
        
                break;
            case 'oracle':
        
                break;
            case 'cassandra':
        
                break;
            case 'mongodb':
        
                break;
            case 'mariadb':
        
                break;
        }
        return $result;
    }
    
    /**
     * 
     * @param string $link
     * @param unknown $dabaseName
     * @param unknown $tableName
     * @param unknown $primaryKey
     * @param unknown $columns
     * @return NULL
     */
    public static function createTable($link = NULL, $dabaseName, $tableName, $primaryKey, $columns = array())
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . $tableName . " (";
        foreach ($columns as $key => $value) {
            switch ($value) {
                case "string":
                    $value = "varchar(255) NOT NULL";
                    break;
                case "integer":
                    $value = "int(11) NOT NULL";
                    break;
                case "float":
                    $value = "float NOT NULL";
                    break;
                case "date":
                    $value = "date NOT NULL";
                    break;
                case "datetime":
                    $value = "datetime NOT NULL";
                    break;
            }
            $sql .= $key . " " . $value . ",";
        }
        $sql .= "PRIMARY KEY (" . $primaryKey . ")";
        $sql .= ")ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
        $resultado = NULL;
        if (is_a($link, "mysqli")) {
            $link->select_db($dabaseName);
            $resultado = $link->query($sql);
            $sql = "ALTER TABLE  " . $tableName . " CHANGE  " . $primaryKey . "  " . $primaryKey . " INT( 11 ) NOT NULL AUTO_INCREMENT ;";
            $link->query($sql);
        } else
            $resultado = self::consulta($sql);
        
        return $resultado;
    }
    
    /**
     * 
     * @param String $user
     * @param String $password
     * @param String $database
     * @param String $type_driver [ optional ]
     * @param String $host [ optional ]
     * @return boolean
     */
    public static function createClassSchema($user, $password, $database , $type_driver = 'mysqli', $host = 'localhost')
    {
        $result_function = false;
        $type_driver = strtolower($type_driver);
        switch ($type_driver) {
            case 'mysqli':
                $con = new \mysqli($host, $user, $password);
                $sql = "use $database;";
                $result = $con->query($sql);
                $sql = "SHOW TABLES;";
                $result = $con->query($sql);
                $lisTables = array();
                $arrayFks = array();
                $lisTablesCreated = array();
                $realPath = explode(DIRECTORY_SEPARATOR ."core", realpath(dirname(__DIR__)));
                if (isset($realPath[0])) {
                    $realPath = $realPath[0];
                }
                $realPath.= DIRECTORY_SEPARATOR. "core".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR;
                if ($result instanceof \mysqli_result) {
                    $subf = "Tables_in_$database";
                    while($row = $result->fetch_assoc()) {
                        $row[$subf] = ucwords(strtolower(trim($row[$subf])));
                        foreach (array('_') as $delimiter) {
                            if (strpos($row["Tables_in_$database"], $delimiter)!==false) {
                                $row[$subf] =implode($delimiter, array_map('ucfirst', explode($delimiter, $row[$subf])));
                            }
                        }
                        $lisTables[] = str_replace("_", "", $row[$subf]);
                    }
                    $sql = "select concat(table_name, '.', column_name) as 'foreign key', concat(referenced_table_name, '.', referenced_column_name) as 'references' from information_schema.key_column_usage where referenced_table_name is not null and table_schema = '$database'";
                    $result = $con->query($sql);
                    if ($result instanceof \mysqli_result) {
                        while($row = $result->fetch_assoc()) {
                            $name_fk = explode(".", $row['foreign key']);
                            $name_fk = $name_fk[1];
                            $tableReference = explode(".", $row['references']);
                            $idTableReference = $tableReference[1];
                            $realTable = $tableReference[0];
                            $tableReference = ucwords(strtolower(trim($tableReference[0])));
                            foreach (array('_') as $delimiter) {
                                if (strpos($tableReference, $delimiter)!==false) {
                                    $tableReference =implode($delimiter, array_map('ucfirst', explode($delimiter, $tableReference)));
                                }
                            }
                            $tableReference = str_replace("_", "", $tableReference);
                            $arrayFks[] = array(
                                "name_champ" => $name_fk,
                                "id_table_reference" => $idTableReference,
                                "table_reference" => $tableReference,
                                "real_table" => $realTable
                            );
                        }
                        foreach ($arrayFks as $value) {
                            $sql = "DESCRIBE ".$value['real_table'].";";
                            $primary_key_table = "";
                            $result = $con->query($sql);
                            if ($result instanceof \mysqli_result) {
                                $variables = array();
                                while ($item = $result->fetch_assoc()) {
                                    $item['Type'] = explode("(", $item['Type']);
                                    $item['Type'] = $item['Type'][0];
                                    if ($item['Key']=='PRI' || $item['Key']=='pri') {
                                        $primary_key_table = $item['Field'];
                                    }
                                    if (true == !empty($item['Extra'])) {
                                        $item['Extra'] = true;
                                    }else{
                                        $item['Extra'] = false;
                                    }
                                    $variables[] = array("name"=>$item['Field'],"type"=>$item['Type'],"index"=>$item['Key'],"isAuto"=>$item['Extra']);
                                }
                                $methods = get_class_methods(__CLASS__);
                                $abstMethods = array();
                                foreach ($methods as $method) {
                                    $reflection = new \ReflectionMethod(__CLASS__, $method);
                                    if (true == $reflection->isAbstract()) {
                                        $arrayParam = array();
                                        $TmparrayParam = $reflection->getParameters();
                                        foreach ($TmparrayParam as $parm) {
                                            if ("empty" !=empty($parm->name)) {
                                                $arrayParam[] = $parm->name;
                                            }
                                        }
                                       $abstMethods[] = array("name_method"=>$reflection->name,"param"=>$arrayParam);
                                    }
                                }
                                $nameNewClass = $realPath . $value['table_reference'] . ".php";
                               
                                if (true == file_exists($nameNewClass)) {
                                    unlink($nameNewClass);    
                                }
                                $nameclass = $value['table_reference'];
$fileClasstext = "
<?php
  * @class: $nameclass (PHP5-Strict with comments)
 * @project: PHP class of AICore framework
 * @date: ".date("Y-m-d")."
 * @version: 1.0.0_php5
 * @author: Zenders Technology S.A de C.V
 * @copyright: Copyright (c) 2017-2020 Zenders Technology S.A de C.V
 * @email: support <at> zenders <dot> mx
 * @descrip: 
 *              Copyright � 2017-2020
 *              Zenders Technology S.A de C.V
 *              All Rights Reserved.
 *  The contents of this file, and the files included with this file, 
 *  are subject to the current version of Common Development and Distribution License, 
 *  Version 1.0 only (the License), which can be obtained at http://zenders.mx/licence. 
 *  You may not use this file except in compliance with the License.
 *  The original code, and all software distributed under the License, are distributed and made 
 *  available on an 'AS IS' basis, WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED. 
 *  Adrian Maldonado Cano HEREBY DISCLAIMS ALL SUCH WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF
 *  MERCHANTABILITY, FITNESS FOR PARTICULAR PURPOSE, OR NON INFRINGEMENT. Please see the License
 *  for the specific language governing rights and limitations under the License.
 *  When distributing Covered Software, include this CDDL Header in each file and include the License file 
 *  at http://zendersolutions.com.mx/licence.  If applicable, add the following below this 
 *  CDDL HEADER, with the fields enclosed by brackets [   ] replaced with your own identifying information: 
 *  Portions Copyright [yyyy]  [name of copyright owner]
 */
namespace classes;
    
use libs\\Database;
class $nameclass extends Database
{      
";
                                $numIndexKey = 0;
                                $Indexes = array();
                                foreach ($variables as $variable) {
                                    if (("MUL" == $variable["index"] || "mul" == $variable["index"] ||
                                        "PRI" == $variable["index"] || "pri" == $variable["index"]) && (false == $variable["isAuto"])) {
                                        $Indexes[] = $variable["name"];
                                        $numIndexKey++;
                                    }
                                }
                                foreach ($variables as $variable) {
                                    $variables[] = array("name"=>$item['Field'],"isIndex"=>$item['Key']);
$fileClasstext.="
    /**
    * @access private 
    * @var ".$variable['type']." ".$variable['name']." 
    */
    private $".$variable['name'].";
        ";
                                   }
$fileClasstext.="
 
    /**
    * 
    */
    function __construct()
    {
        parent::__construct();
    }
 
    /**
    * 
    */
    function __destruct()
    {
        parent::__destruct();
    }
    
";
                                    foreach ($abstMethods as $abstMethod) {
                                        $numArg = count($abstMethod['param']);
                                        $h =0;
$fileClasstext .= "
    
    /**
	 * (non-PHPdoc)
	 * @see \libs\Database::set()
	 */
    public function ".$abstMethod['name_method']."(";
                                        $arrayParambody = array();
                                        foreach ($abstMethod['param'] as $param) {
                                            $arrayParambody[]="$".$param;
                                            if ($h<($numArg-1)) {
                                                $fileClasstext.= "$".$param." , ";
                                            }else{
                                                $fileClasstext.= "$".$param;
                                            }
                                            $h++;
                                        }
$fileClasstext .="){";
                                    switch ($abstMethod['name_method']) {
                                           case "set":
$fileClasstext .='
        $result = -1 ;';
    $j = 0;
    if ($h > 0) {
        $fileClasstext .="
        if (";
        foreach ($arrayParambody as $paramBody) {
            if ($j < ($h-1)) {
                $fileClasstext .="true == !empty($paramBody) &&";
            } else{
                $fileClasstext .="true == !empty($paramBody) ";
            }
            $j++;
        }
        $fileClasstext .=') {';
    }
if ($numIndexKey > 0 ) {
$fileClasstext .='
            if (';
}
                $it = 0;
               foreach ($Indexes as $indx) {
                   if ($it <($numIndexKey-1)) {
$fileClasstext .='true == key_exists("'.$indx.'", $data) && ';
                   }else{
$fileClasstext .='true == key_exists("'.$indx.'", $data)';                       
                   }
                   $it++;
               }
if ($numIndexKey > 0 ) {
$fileClasstext .=') {
'; 
}
$fileClasstext .='              $result = parent::insertRow("'.$value['real_table'].'",$data);';
if ($numIndexKey > 0 ) {     
$fileClasstext .='
            }';
}
if ($h > 0) {
    $fileClasstext .='
        }
';
}
$fileClasstext .='
        return $result;
';                                        
                                            break;
                                        case "edit":
                                            $fileClasstext .='
        $result = -1 ;';
                                        $j = 0;
                                        if ($h > 0) {
                                            $fileClasstext .="
        if (";
                                            foreach ($arrayParambody as $paramBody) {
                                                if ($j < ($h-1)) {
                                                    $fileClasstext .="true == !empty($paramBody) && ";
                                                } else{
                                                    $fileClasstext .="true == !empty($paramBody) ";
                                                }
                                                $j++;
                                            }
                                            $fileClasstext .=') {';
                                        }
$fileClasstext .='
            $clause = \' '.$primary_key_table.' ="\'.$key.\'"\';
            $result = parent::udateRow("'.$value['real_table'].'",$data,$clause);';
if ($h > 0) {
    $fileClasstext .='
        }
';
}           
                                            $fileClasstext .='
        return $result;
';
                                            break;
                                        case "delete":
                                            $fileClasstext .='
        $result = -1 ;';
                                            $j = 0;
                                            if ($h > 0) {
                                                $fileClasstext .="
        if (";
                                                foreach ($arrayParambody as $paramBody) {
                                                    if ($j < ($h-1)) {
                                                        $fileClasstext .="true == !empty($paramBody) && ";
                                                    } else{
                                                        $fileClasstext .="true == !empty($paramBody) ";
                                                    }
                                                    $j++;
                                                }
                                                $fileClasstext .=') {';
                                            }
                                            $fileClasstext .='
            $clause = \' '.$primary_key_table.' ="\'.$key.\'"\';
            $result = parent::dropRow("'.$value['real_table'].'",$clause);';
                                            if ($h > 0) {
                                                $fileClasstext .='
        }
';
                                            }
                                            $fileClasstext .='
        return $result;
';
                                            break;
                                        case "exists":
                                            $fileClasstext .='
        $result = false;';
                                            $j = 0;
                                            if ($h > 0) {
                                                $fileClasstext .="
        if (";
                                                foreach ($arrayParambody as $paramBody) {
                                                    if ($j < ($h-1)) {
                                                        $fileClasstext .="true == !empty($paramBody) && ";
                                                    } else{
                                                        $fileClasstext .="true == !empty($paramBody) ";
                                                    }
                                                    $j++;
                                                }
                                                $fileClasstext .=') {';
                                            }
                                            $fileClasstext .='
            $table = \''.$value['real_table'].'\';
            $result = parent::findOneBySql("SELECT '.$primary_key_table.' FROM $table  where '.$primary_key_table.' =\'?\' ",$key);
            if (true ==!empty($result)) {
                $result = true;
            }
            ';
                                            if ($h > 0) {
                                                $fileClasstext .='
        }
';
                                            }
                                            $fileClasstext .='
        return $result;
';
                                            break;
                                    }
$fileClasstext .="  }";
                                    }
$fileClasstext.="
    
    }
    ?>
";
                                    $fh = fopen($nameNewClass, 'a');
                                    fwrite($fh, $fileClasstext);
                                    fclose($fh);
                            }
                        }
                    }
                    
                }
                
                break;
            case 'mysql':
        
                break;
            case 'postgresql':
        
                break;
            case 'oracle':
        
                break;
            case 'cassandra':
        
                break;
            case 'mongodb':
        
                break;
            case 'mariadb':
        
                break;
        }
        return $result_function;
    }
}
?>