<?php

namespace FormGenerator\Validation;

class ValidationFactory{
    
    public static function creatElement($config)
    {
        $rule = "FormGenerator\\Validation\\" . $config['class'];
        $element = new $rule($config);
        return $element;
    }
}