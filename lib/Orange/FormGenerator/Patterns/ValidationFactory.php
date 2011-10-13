<?php

namespace FormGenerator\Patterns;

class ValidationFactory{
    
    public static function creatElement($config)
    {
        $rule = "FormGenerator\\Validation\\" . $config['class'];
        $element = new $rule($config);
        return $element;
    }
}