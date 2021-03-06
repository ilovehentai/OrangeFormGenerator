<?php

namespace FormGenerator\FormElements;

class ElementFactory{
    
    public static function creatElement($config)
    {
        $type = "FormGenerator\\FormElements\\" . ucfirst($config['type'])."Element";
        $element = new $type($config);
        $element->set_mId($config['id']);
        return $element;
    }
}