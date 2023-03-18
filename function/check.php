<?php

class check{

    public function check_Email($email){
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
        preg_match($pattern,$email,$res);
        return $res;
    }

    public function check_Date($date){
        $pattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";
        preg_match($pattern,$date,$res);
        return $res;
    }

    public function check_Number($num){
        $pattern = "/^[1-9][0-9]*$/";
        preg_match($pattern,$num,$res);
        return $res;
    }

    public function check_Password($password){
        $pattern = "/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\d]){1,})(?=(.*[\W]){1,})(?!.*\s).{8,}$/";
        preg_match($pattern,$password,$res);
        return $res;
    }

}