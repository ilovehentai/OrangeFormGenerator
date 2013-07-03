<?php

namespace FormGenerator\Validation;

class NotNullValidation extends BaseValidation{
    
    public function __construct($config) {
        parent::__construct($config);
        $this->_mExpression = "//";
    }
    
    public function isValid($value) {
        if(!is_null($value))
        {   
            return true;
        }
        return false;
        
    }
    
}
