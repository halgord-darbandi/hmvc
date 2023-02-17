<?php

namespace App\Validation;

class Validation{

    public static function validate($data){
        if (empty($data)){
            throw new \Exception();
        }
        return htmlentities($data);
    }
}