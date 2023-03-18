<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class func{

    private $conn;

    public function __construct(){
        require_once "../config/database.php";
        require_once "check.php";
        require_once "AES.php";
        
        $database = new database();
        $this->conn = $database->getConnection();

        $this->check = new check();
        $this->ASE = new AES();
    }

    public function get_UrlOptions(){
        $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $url_parse = parse_url($url);
        $res = $this->convertUrlArray($url_parse['query']);
        $res_return = array();
        foreach ($res as $r){
            array_push($res_return,$this->ASE->decrypt($r));
        }
        return $res_return;
    }

    public function convertUrlArray($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;

    }

    public function check_Token(){
        //token_origin = $2y$10$z498Zm8y7V8WZSpzDs9ioeF.bZ4hwoXmZC.6nnNVnZfY.pNj29oR6;
        $url = $this->get_UrlOptions();
        if ($url[1] === "Jiojio000608."){
            //var_dump($this->ASE->encrypt("Jiojio000608."));
            return true;
        }else{
            return false;
        }
    }

    public function call_function($name_function,$options){
        //options type = array
        //$name_function type = string
        return call_user_func_array(array($this,$name_function),$options);
    }

    public function set_options(){
        $url = $this->get_UrlOptions();
        $options = array();
        foreach ($url as $u){
            array_push($options,$u);
        }
        unset($options[0]);
        unset($options[1]);

        return $options;
    }

    //http://localhost/api/function/func.php?func=get_All_from_Table&token=$2y$10$z498Zm8y7V8WZSpzDs9ioeF.bZ4hwoXmZC.6nnNVnZfY.pNj29oR6&t=clients
    public function get_All_from_Table($name_table){
        $sql = "SELECT * FROM " . $name_table;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    //http://localhost/api/function/func.php?func=get_By_value&token=$2y$10$z498Zm8y7V8WZSpzDs9ioeF.bZ4hwoXmZC.6nnNVnZfY.pNj29oR6&t=clients&condition=id&id=100083
    public function get_By_value($name_table,$value){
        $values = array();
        foreach (explode(",",$value) as $v){
            if (!empty($v) && $v !== "" && $v !== " "){
                array_push($values,$v);
            }
        }
        $sql = "SELECT * FROM " . $name_table . " WHERE " . $values[0] . " = '" . $values[1] . "'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    //http://localhost/api/function/func.php?func=inscription&token=$2y$10$z498Zm8y7V8WZSpzDs9ioeF.bZ4hwoXmZC.6nnNVnZfY.pNj29oR6&t=clients&values=hou,zeyu,1998-05-12,houzeyu7@gmail.com,33 rue louise weiss 75013 paris,Jiojio000608.,0695867276
    public function inscription($name_table,$options){
        $values = array();
        foreach (explode(",",$options) as $v){
            if (!empty($v) && $v !== "" && $v !== " "){
                array_push($values,$v);
            }
        }
        if (sizeof($values) === 7){
            if ($this->check->check_Email($values[3]) &&
                $this->check->check_Date($values[2]) &&
                $this->check->check_Number(intval($values[6])) &&
                $this->check_DoubleUser($values[3]) &&
                $this->check->check_Password($values[5])){
                $values[5] = password_hash($values[5],PASSWORD_BCRYPT );
                $sql = "INSERT INTO " . $name_table . "(nom,prenom,date_de_naissance,email,address,password,tele) VALUES (?,?,?,?,?,?,?)";
                $stmt = $this->conn->prepare($sql);
                json_encode($stmt->execute($values));
            }else{
                json_encode("false");
            }
        }else{
            json_encode("false");
        }

    }

    public function login($table_name,$options){
        $values = array();
        foreach (explode(",",$options) as $v){
            if (!empty($v) && $v !== "" && $v !== " "){
                array_push($values,$v);
            }
        }
        $sql = "SELECT id,email,password FROM " . $table_name . " WHERE email = " . "'" . $values[0] . "'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res["email"] === $values[0] && password_verify($values[1],$res["password"])){
            echo json_encode($res);

        }else{
            echo json_encode($res);

        }
    }

    private function check_DoubleUser($email){
        $sql = "SELECT id from clients WHERE email = " . "'" .$email . "'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $num_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($num_user){
            return false;
        }else{
            return true;
        }
    }

    public function test(){
        require_once "AES.php";
        $Ase = new AES();
        $res_crypt = $Ase->encrypt("houzeyu7@gmail.com,Jiojio000608.");
        var_dump($res_crypt);
        $res_decrypt = $Ase->decrypt("KmwHIJiFla9W0MTlRAwdZg==");
        var_dump($res_decrypt);
        return true;
    }
}

$function = new func();
$token = $function->check_Token();

if ($token === true){
    $url = $function->get_UrlOptions();
    $options = $function->set_options();
    $res = $function->call_function($url[0],$options);
    //echo json_encode($res);
}else{
    header('HTTP/1.0 403 Forbidden');
    json_encode("403");
}